<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Kilometer;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use OpenSpout\Reader\CSV\Reader as CsvReader;
use OpenSpout\Reader\XLSX\Reader as XlsxReader;

class KilometerImportService
{
    /**
     * Import kilometers from an uploaded Excel or CSV file.
     * Note: Ignores invoice_name column and does not sync to groups table as requested.
     *
     * @param string $filePath Absolute path to the file
     * @param int|null $targetGroupId Target group ID (null for all groups)
     * @param string $duplicateMode 'skip' | 'update' | 'append'
     * @param User|null $currentUser Current logged in user
     * @return array
     */
    public function import(
        string $filePath,
        ?int $targetGroupId = null,
        string $duplicateMode = 'skip',
        ?User $currentUser = null
    ): array {
        @set_time_limit(300);
        @ini_set('memory_limit', '512M');

        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $reader = ($ext === 'csv') ? new CsvReader() : new XlsxReader();
        $reader->open($filePath);

        // Preload group users: group_id => user_id
        $groupUsers = User::query()
            ->where('role', 'group')
            ->whereNotNull('group_id')
            ->pluck('id', 'group_id')
            ->toArray();

        // Preload existing kilometers for fast in-memory O(1) duplicate checks
        // Key format: group_id|normalized_address|ward|district|package_note
        $existingKmMap = [];
        $queryKm = Kilometer::query();
        if ($targetGroupId) {
            $queryKm->where('group_id', $targetGroupId);
        }
        foreach ($queryKm->get(['id', 'group_id', 'address', 'ward', 'district', 'package_note']) as $km) {
            $key = $this->buildKey(
                (int) $km->group_id,
                (string) $km->address,
                (string) $km->ward,
                (string) $km->district,
                (string) $km->package_note
            );
            $existingKmMap[$key] = $km->id;
        }

        $totalRowsRead = 0;
        $importedCount = 0;
        $updatedCount = 0;
        $skippedCount = 0;
        $groupsAffected = [];

        DB::beginTransaction();

        try {
            foreach ($reader->getSheetIterator() as $sheet) {
                $sheetName = $sheet->getName();

                // Detect group from sheet name (e.g. "nhóm 1", "nhóm 14", "nhóm2")
                $sheetGroup = null;
                if (preg_match('/nh[oóòọõôốồộỗơớờợ]m\s*(\d+)/ui', $sheetName, $match)) {
                    $sheetGroup = (int) $match[1];
                }

                // If filtering by a specific group and this sheet belongs to another group, skip sheet entirely
                if ($targetGroupId && $sheetGroup && $sheetGroup !== $targetGroupId) {
                    continue;
                }

                $colIndexMap = null;
                $rowNumber = 0;

                foreach ($sheet->getRowIterator() as $row) {
                    $rowNumber++;
                    $rawCells = array_map(fn ($c) => trim((string) $c->getValue()), $row->getCells());

                    // First row is header
                    if ($rowNumber === 1) {
                        $colIndexMap = $this->detectColumns($rawCells);
                        continue;
                    }

                    // Skip empty rows
                    if (empty(array_filter($rawCells))) {
                        continue;
                    }

                    $totalRowsRead++;

                    // Determine group ID for this row
                    $rowGroup = $sheetGroup;
                    if (!$rowGroup && isset($rawCells[$colIndexMap['group']])) {
                        if (preg_match('/nh[oóòọõôốồộỗơớờợ]m\s*(\d+)/ui', $rawCells[$colIndexMap['group']], $m)) {
                            $rowGroup = (int) $m[1];
                        } elseif (is_numeric($rawCells[$colIndexMap['group']])) {
                            $rowGroup = (int) $rawCells[$colIndexMap['group']];
                        }
                    }

                    // Fallback to target group if still null
                    if (!$rowGroup && $targetGroupId) {
                        $rowGroup = $targetGroupId;
                    }

                    // If still no group or doesn't match target group, skip
                    if (!$rowGroup || ($targetGroupId && $rowGroup !== $targetGroupId)) {
                        continue;
                    }

                    // Extract columns (strictly ignoring invoice_name / Tên xuất phiếu)
                    $address = $rawCells[$colIndexMap['address']] ?? '';
                    $ward = $rawCells[$colIndexMap['ward']] ?? '';
                    $district = $rawCells[$colIndexMap['district']] ?? '';
                    $kmRaw = str_replace(',', '.', $rawCells[$colIndexMap['km']] ?? '0');
                    $feeRaw = preg_replace('/[^\d]/', '', $rawCells[$colIndexMap['fee']] ?? '0');
                    $packageNote = $rawCells[$colIndexMap['note']] ?? '';

                    // Validate minimum content: if address is empty or just '-', skip
                    if ($address === '' || $address === '-') {
                        $skippedCount++;
                        continue;
                    }

                    $distanceKm = is_numeric($kmRaw) ? max(0, round((float) $kmRaw, 2)) : 0.0;
                    $carrierFee = is_numeric($feeRaw) ? max(0, (float) $feeRaw) : 0.0;
                    $ownerUserId = $groupUsers[$rowGroup] ?? $currentUser?->id;

                    // Build duplicate identification key
                    $dupKey = $this->buildKey($rowGroup, $address, $ward, $district, $packageNote);

                    if (isset($existingKmMap[$dupKey])) {
                        if ($duplicateMode === 'skip') {
                            $skippedCount++;
                            continue;
                        }

                        if ($duplicateMode === 'update') {
                            $existingKmId = $existingKmMap[$dupKey];
                            Kilometer::where('id', $existingKmId)->update([
                                'ward' => $ward ?: '',
                                'district' => $district ?: '',
                                'distance_km' => $distanceKm,
                                'carrier_fee' => $carrierFee,
                                'package_note' => $packageNote ?: null,
                            ]);

                            $updatedCount++;
                            $groupsAffected[$rowGroup] = ($groupsAffected[$rowGroup] ?? 0) + 1;
                            continue;
                        }
                    }

                    // Create new Kilometer record (without invoice_name)
                    $newKm = Kilometer::create([
                        'group_id' => $rowGroup,
                        'owner_user_id' => $ownerUserId,
                        'date' => now()->toDateString(),
                        'address' => $address ?: 'Chưa cập nhật',
                        'ward' => $ward ?: '',
                        'district' => $district ?: '',
                        'distance_km' => $distanceKm,
                        'carrier_fee' => $carrierFee,
                        'motorbike_driver' => null,
                        'package_note' => $packageNote ?: null,
                    ]);

                    $existingKmMap[$dupKey] = $newKm->id;
                    $importedCount++;
                    $groupsAffected[$rowGroup] = ($groupsAffected[$rowGroup] ?? 0) + 1;
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $reader->close();
            throw $e;
        }

        $reader->close();

        // Format summary of affected groups
        ksort($groupsAffected);
        $groupDetails = [];
        foreach ($groupsAffected as $grpId => $cnt) {
            $groupDetails[] = "Nhóm {$grpId}: {$cnt} dòng";
        }
        $groupSummary = !empty($groupDetails) ? implode(', ', $groupDetails) : 'Không có dòng mới';

        // Log Activity
        ActivityLog::createLog(
            description: "Import kilomet từ file Excel: Đã thêm mới {$importedCount} dòng, cập nhật {$updatedCount} dòng, bỏ qua {$skippedCount} dòng trùng ({$groupSummary})",
            module: 'kilometers',
            action: 'create',
            user: $currentUser
        );

        return [
            'success' => true,
            'total_rows_read' => $totalRowsRead,
            'imported' => $importedCount,
            'updated' => $updatedCount,
            'skipped' => $skippedCount,
            'groups_affected' => $groupsAffected,
            'group_summary' => $groupSummary,
        ];
    }

    /**
     * Map flexible headers to standardized column indices.
     */
    private function detectColumns(array $headers): array
    {
        $map = [
            'group' => 0,
            'address' => 2,
            'ward' => 3,
            'district' => 4,
            'km' => 5,
            'fee' => 6,
            'note' => 7,
        ];

        foreach ($headers as $index => $header) {
            $clean = mb_strtoupper(trim($header), 'UTF-8');
            if (str_contains($clean, 'NHÓM') || str_contains($clean, 'NHOM')) {
                $map['group'] = $index;
            } elseif (str_contains($clean, 'ĐỊA CHỈ') || str_contains($clean, 'DIA CHI') || str_contains($clean, 'ADDRESS')) {
                $map['address'] = $index;
            } elseif (str_contains($clean, 'PHƯỜNG') || str_contains($clean, 'PHUONG') || str_contains($clean, 'WARD')) {
                $map['ward'] = $index;
            } elseif (str_contains($clean, 'QUẬN') || str_contains($clean, 'QUAN') || str_contains($clean, 'DISTRICT')) {
                $map['district'] = $index;
            } elseif (str_contains($clean, 'KILOMET') || str_contains($clean, 'KM') || str_contains($clean, 'DISTANCE')) {
                $map['km'] = $index;
            } elseif (str_contains($clean, 'CHÀNH') || str_contains($clean, 'CHANH') || str_contains($clean, 'FEE')) {
                $map['fee'] = $index;
            } elseif (str_contains($clean, 'GHI BAO') || str_contains($clean, 'GHI CHÚ') || str_contains($clean, 'NOTE')) {
                $map['note'] = $index;
            }
        }

        return $map;
    }

    /**
     * Build normalized key for duplicate detection.
     */
    private function buildKey(int $groupId, string $address, string $ward, string $district, string $packageNote): string
    {
        $normAddr = mb_strtolower(preg_replace('/\s+/', ' ', trim($address)), 'UTF-8');
        $normWard = mb_strtolower(preg_replace('/\s+/', ' ', trim($ward)), 'UTF-8');
        $normDist = mb_strtolower(preg_replace('/\s+/', ' ', trim($district)), 'UTF-8');
        $normNote = mb_strtolower(preg_replace('/\s+/', ' ', trim($packageNote)), 'UTF-8');

        return "{$groupId}|{$normAddr}|{$normWard}|{$normDist}|{$normNote}";
    }
}
