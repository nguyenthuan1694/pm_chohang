<?php

namespace App\Http\Controllers;

use App\Models\CargoDelivery;
use App\Models\Employee;
use Illuminate\Http\Request;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Writer\XLSX\Options;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Border;
use OpenSpout\Common\Entity\Style\BorderPart;

class BillingController extends Controller
{
    /**
     * Display a listing of billing data.
     */
    public function index(Request $request)
    {
        $employees = Employee::orderBy('name')->get();

        $isInitialLoad = !$request->has('from_date') && !$request->has('to_date') && !$request->has('employee_id');
        $today = now()->toDateString();

        if ($isInitialLoad) {
            $fromDate = $today;
            $toDate = $today;
            $employeeId = null;
            $hasSearched = false;
        } else {
            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');
            $employeeId = $request->input('employee_id');
            $hasSearched = true;
        }

        $viewMode = $request->input('view_mode', 'daily'); // 'daily' or 'detail'

        // Query only delivered cargo deliveries
        $query = CargoDelivery::with(['employee', 'group'])
            ->where('delivery_status', 'delivered');

        if ($fromDate) {
            $query->whereDate('delivery_date', '>=', $fromDate);
        }
        if ($toDate) {
            $query->whereDate('delivery_date', '<=', $toDate);
        }
        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        $deliveries = $query->orderBy('delivery_date', 'asc')
            ->orderBy('trip_count', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $calculationResult = $this->calculateBillingData($deliveries);

        return view('billing.index', [
            'employees' => $employees,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'employeeId' => $employeeId,
            'viewMode' => $viewMode,
            'hasSearched' => $hasSearched,
            'isToday' => ($fromDate === $today && $toDate === $today && empty($employeeId)),
            'dailySummaries' => $calculationResult['daily_summaries'],
            'allDetails' => $calculationResult['all_details'],
            'totals' => $calculationResult['totals'],
            'selectedEmployee' => $employeeId ? $employees->firstWhere('id', $employeeId) : null,
        ]);
    }

    /**
     * Export billing data as XLSX matching BANG TINH CHANH VA KILOMET.xlsx structure.
     */
    public function export(Request $request)
    {
        $isInitialLoad = !$request->has('from_date') && !$request->has('to_date') && !$request->has('employee_id');
        $today = now()->toDateString();

        if ($isInitialLoad) {
            $fromDate = $today;
            $toDate = $today;
            $employeeId = null;
        } else {
            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');
            $employeeId = $request->input('employee_id');
        }

        $mode = $request->input('mode', 'standard'); // 'standard' (Bang Tinh Chanh & KM), 'daily' (Tong hop theo ngay)

        $query = CargoDelivery::with(['employee', 'group'])
            ->where('delivery_status', 'delivered');

        if ($fromDate) {
            $query->whereDate('delivery_date', '>=', $fromDate);
        }
        if ($toDate) {
            $query->whereDate('delivery_date', '<=', $toDate);
        }
        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        $deliveries = $query->orderBy('delivery_date', 'asc')
            ->orderBy('trip_count', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $calculationResult = $this->calculateBillingData($deliveries);

        // Fallback to CSV if ZipArchive extension is not enabled yet in web server
        if (!class_exists('ZipArchive')) {
            return $this->exportCsvFallback($mode, $calculationResult);
        }

        // Thin border style
        $thinBorder = new Border(
            new BorderPart(Border::TOP, 'D3D3D3', Border::WIDTH_THIN, Border::STYLE_SOLID),
            new BorderPart(Border::RIGHT, 'D3D3D3', Border::WIDTH_THIN, Border::STYLE_SOLID),
            new BorderPart(Border::BOTTOM, 'D3D3D3', Border::WIDTH_THIN, Border::STYLE_SOLID),
            new BorderPart(Border::LEFT, 'D3D3D3', Border::WIDTH_THIN, Border::STYLE_SOLID)
        );

        $headerStyle = (new Style())
            ->setFontBold()
            ->setFontSize(11)
            ->setFontName('Times New Roman')
            ->setBackgroundColor('FFF2CC')
            ->setBorder($thinBorder);

        $dataStyle = (new Style())
            ->setFontSize(11)
            ->setFontName('Times New Roman')
            ->setBorder($thinBorder);

        $boldTotalStyle = (new Style())
            ->setFontBold()
            ->setFontSize(11)
            ->setFontName('Times New Roman')
            ->setBackgroundColor('E2EFDA')
            ->setBorder($thinBorder);

        $tempFile = tempnam(sys_get_temp_dir(), 'export_') . '.xlsx';
        $options = new Options();

        if ($mode === 'daily') {
            // Options column widths for daily summary
            $options->setColumnWidth(8, 1);   // STT
            $options->setColumnWidth(14, 2);  // NGÀY
            $options->setColumnWidth(25, 3);  // TÊN CHỞ HÀNG
            $options->setColumnWidth(22, 4);  // LOẠI NHÂN VIÊN
            $options->setColumnWidth(12, 5);  // SỐ CHUYẾN
            $options->setColumnWidth(14, 6);  // SỐ ĐIỂM GIAO
            $options->setColumnWidth(16, 7);  // SỐ BAO
            $options->setColumnWidth(12, 8);  // SỐ KG
            $options->setColumnWidth(18, 9);  // TIỀN CHÀNH
            $options->setColumnWidth(20, 10); // TỔNG KM TÍNH TIỀN
            $options->setColumnWidth(16, 11); // XE ÔM

            $writer = new Writer($options);
            $writer->openToFile($tempFile);

            $sheet = $writer->getCurrentSheet();
            $sheet->setName('TONG HOP THEO NGAY');

            $headersDaily = [
                'STT',
                'NGÀY',
                'TÊN CHỞ HÀNG',
                'LOẠI NHÂN VIÊN',
                'SỐ CHUYẾN',
                'SỐ ĐIỂM GIAO',
                'SỐ BAO',
                'SỐ KG',
                'TIỀN CHÀNH (VNĐ)',
                'TỔNG KM TÍNH TIỀN',
                'XE ÔM',
            ];
            $writer->addRow(Row::fromValues($headersDaily, $headerStyle));

            $stt = 1;
            foreach ($calculationResult['daily_summaries'] as $day) {
                $writer->addRow(Row::fromValues([
                    $stt++,
                    $day->date_formatted,
                    $day->employee_name,
                    $day->is_support ? 'Hỗ trợ (0km đầu)' : 'Thường (trừ 2km đầu)',
                    $day->trip_count,
                    $day->order_count,
                    $day->package_count_text,
                    (float) $day->total_weight_kg,
                    (float) $day->total_carrier_fee,
                    (float) $day->total_distance_km,
                    $day->motorbike_driver,
                ], $dataStyle));
            }

            // Total row
            $writer->addRow(Row::fromValues([
                'TỔNG CỘNG',
                $calculationResult['totals']['total_days'] . ' ngày',
                '',
                '',
                $calculationResult['totals']['total_trips'] . ' chuyến',
                $calculationResult['totals']['total_orders'] . ' điểm',
                '',
                (float) $calculationResult['totals']['total_weight_kg'],
                (float) $calculationResult['totals']['total_carrier_fee'],
                (float) $calculationResult['totals']['total_distance_km'],
                '',
            ], $boldTotalStyle));

            $writer->close();
            $filename = 'BANG_TINH_KILOMET_THEO_NGAY_' . now()->format('Ymd_His') . '.xlsx';
        } else {
            // Standard mode: Matches BANG TINH CHANH VA KILOMET.xlsx structure exactly
            $options->setColumnWidth(14, 1);  // NGÀY
            $options->setColumnWidth(10, 2);  // NHÓM
            $options->setColumnWidth(28, 3);  // CHÀNH XE - CỬA HÀNG
            $options->setColumnWidth(32, 4);  // ĐỊA CHỈ
            $options->setColumnWidth(18, 5);  // PHƯỜNG
            $options->setColumnWidth(14, 6);  // QUẬN
            $options->setColumnWidth(10, 7);  // KM
            $options->setColumnWidth(14, 8);  // TIỀN CHÀNH
            $options->setColumnWidth(25, 9);  // TÊN CHỞ HÀNG / THÔNG TIN GHI BAO
            $options->setColumnWidth(12, 10); // SỐ CHUYẾN
            $options->setColumnWidth(12, 11); // SỐ BAO
            $options->setColumnWidth(12, 12); // SỐ KG
            $options->setColumnWidth(16, 13); // TIEN CHANH
            $options->setColumnWidth(16, 14); // TONG KM
            $options->setColumnWidth(12, 15); // NGÀY (DD/MM/YY)
            $options->setColumnWidth(14, 16); // XE ÔM

            $writer = new Writer($options);
            $writer->openToFile($tempFile);

            // 1. SHEET GOC (toàn bộ đơn hàng)
            $sheetGoc = $writer->getCurrentSheet();
            $sheetGoc->setName('GOC');

            $headersGoc = [
                'NGÀY', 'NHÓM', 'CHÀNH XE - CỬA HÀNG', 'ĐỊA CHỈ ', 'PHƯỜNG ', 'QUẬN ',
                'KM', 'TIỀN CHÀNH', 'THÔNG TIN GHI BAO', 'TÊN CHỞ HÀNG', 'SỐ CHUYẾN', 'SỐ BAO', 'SỐ KG'
            ];
            $writer->addRow(Row::fromValues($headersGoc, $headerStyle));

            foreach ($calculationResult['all_details'] as $item) {
                $writer->addRow(Row::fromValues([
                    $item->delivery_date ? $item->delivery_date->format('d/m/Y') : '',
                    $item->group_name,
                    $item->invoice_name,
                    $item->address,
                    $item->ward,
                    $item->district,
                    (float) $item->distance_km,
                    (float) $item->carrier_fee,
                    $item->package_note,
                    $item->employee?->name ?? '',
                    $item->trip_count,
                    $item->package_count,
                    $item->weight_kg,
                ], $dataStyle));
            }

            // 2. SHEETS CHO TỪNG NHÂN VIÊN CHỞ HÀNG
            $groupedByEmployee = collect($calculationResult['all_details'])->groupBy(function ($item) {
                return $item->employee_id ?? 0;
            });

            $usedSheetNames = ['GOC' => true];
            $headersEmp = [
                'NGÀY', 'NHÓM', 'CHÀNH XE - CỬA HÀNG', 'ĐỊA CHỈ ', 'PHƯỜNG ', 'QUẬN ',
                'KM', 'TIỀN CHÀNH', 'TÊN CHỞ HÀNG', 'SỐ CHUYẾN', 'SỐ BAO', 'SỐ KG',
                "TIEN\nCHANH", 'TONG KM', '', 'XE ÔM'
            ];

            foreach ($groupedByEmployee as $empId => $empItems) {
                $firstItem = $empItems->first();
                $rawEmpName = $firstItem->employee?->name ?? ($empId ? 'NV ' . $empId : 'CHƯA GÁN');
                $isSupport = (bool) ($firstItem->employee?->is_support ?? false);

                // Clean sheet name: max 31 chars, remove invalid chars \ / ? * : [ ]
                $cleanName = preg_replace('/[\\\\\/\?\*\:\[\]]/', '', $rawEmpName);
                $cleanName = mb_strtoupper(trim($cleanName));
                if ($cleanName === '') {
                    $cleanName = 'NV ' . $empId;
                }
                $cleanName = mb_substr($cleanName, 0, 31);

                $sheetName = $cleanName;
                $counter = 2;
                while (isset($usedSheetNames[$sheetName])) {
                    $sheetName = mb_substr($cleanName, 0, 28) . ' ' . $counter;
                    $counter++;
                }
                $usedSheetNames[$sheetName] = true;

                $sheet = $writer->addNewSheetAndMakeItCurrent();
                $sheet->setName($sheetName);

                // Row 1: Blank
                $writer->addRow(Row::fromValues([]));

                // Row 2: M2 = 'KM ĐẦU:', N2 = 2 (regular) or blank (support)
                $cellsRow2 = [];
                for ($i = 0; $i < 12; $i++) {
                    $cellsRow2[] = Cell::fromValue('');
                }
                $cellsRow2[] = Cell::fromValue('KM ĐẦU:');
                $cellsRow2[] = Cell::fromValue($isSupport ? '' : 2);
                $writer->addRow(new Row($cellsRow2));

                // Row 3: Header
                $writer->addRow(Row::fromValues($headersEmp, $headerStyle));

                // Row 4+: Data items
                foreach ($empItems as $item) {
                    $writer->addRow(Row::fromValues([
                        $item->delivery_date ? $item->delivery_date->format('d/m/Y') : '',
                        $item->group_name,
                        $item->invoice_name,
                        $item->address,
                        $item->ward,
                        $item->district,
                        (float) $item->distance_km,
                        (float) $item->carrier_fee,
                        $item->employee?->name ?? '',
                        $item->trip_count,
                        $item->package_count,
                        $item->weight_kg,
                        (float) $item->calculated_carrier_fee,
                        (float) $item->calculated_km,
                        $item->delivery_date ? $item->delivery_date->format('d/m/y') : '',
                        $item->motorbike_driver ?: '',
                    ], $dataStyle));
                }
            }

            $writer->close();
            $filename = 'BANG_TINH_CHANH_VA_KILOMET_' . now()->format('Ymd_His') . '.xlsx';
        }

        return response()->download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Compute billing values according to the Excel formulas:
     * - Regular employee (is_support = false): 2 km base deduction per trip (Sheet ANH KHOA: $N$2 = 2).
     *   Trip billable km = max(0, MAX(distance_km) - 2).
     * - Support employee (is_support = true): 0 km deduction per trip (Sheet VŨ THẮNG: $N$2 is empty/0).
     *   Trip billable km = max(0, MAX(distance_km) - 0) = MAX(distance_km).
     * - Item allocated km = (trip_billable_km / SUM(distance_km)) * item_distance_km.
     * - Item carrier fee = fee divided equally among duplicate destinations in the same trip.
     */
    private function calculateBillingData($deliveries): array
    {
        // Group by delivery date and employee (Y-m-d_employee_id) so trips are never mixed across different employees
        $groupedByDateEmployee = $deliveries->groupBy(function ($item) {
            $dateStr = $item->delivery_date ? $item->delivery_date->format('Y-m-d') : 'unknown';
            return $dateStr . '_' . ($item->employee_id ?? '0');
        });

        $dailySummaries = [];
        $allDetails = [];

        $grandTotalTrips = 0;
        $grandTotalOrders = $deliveries->count();
        $grandTotalWeight = 0.0;
        $grandTotalCarrierFee = 0.0;
        $grandTotalKm = 0.0;

        foreach ($groupedByDateEmployee as $dateEmpKey => $dayItems) {
            $firstItem = $dayItems->first();
            $isSupport = (bool) ($firstItem->employee?->is_support ?? false);
            // Sheet ANH KHOA (regular employee) has $N$2 = 2 (deduct 2km per trip).
            // Sheet VŨ THẮNG (support employee) has $N$2 = 0 / empty (no deduction).
            $kmDau = $isSupport ? 0.0 : 2.0;

            // Group by trip within that employee's day
            $groupedByTrip = $dayItems->groupBy(function ($item) {
                return (int) $item->trip_count;
            });

            $dayTripsCount = $groupedByTrip->count();
            $grandTotalTrips += $dayTripsCount;

            foreach ($groupedByTrip as $tripNumber => $tripItems) {
                $maxKm = (float) ($tripItems->max('distance_km') ?? 0);
                $sumKm = (float) ($tripItems->sum('distance_km') ?? 0);
                $tripBillableKm = max(0.0, $maxKm - $kmDau);

                // Group by address within the same trip to split carrier fee
                $groupedByAddress = $tripItems->groupBy(function ($item) {
                    return trim(mb_strtolower((string) $item->address));
                });

                foreach ($tripItems as $item) {
                    $item->is_support = $isSupport;

                    // Allocated KM
                    if ($sumKm > 0) {
                        $item->calculated_km = round(($tripBillableKm / $sumKm) * (float) $item->distance_km, 2);
                    } else {
                        $item->calculated_km = 0.0;
                    }

                    // Allocated Carrier fee (if duplicate address in same trip)
                    $addressKey = trim(mb_strtolower((string) $item->address));
                    $sameAddressCount = isset($groupedByAddress[$addressKey]) ? $groupedByAddress[$addressKey]->count() : 1;
                    if ($sameAddressCount > 1) {
                        $item->calculated_carrier_fee = round(((float) $item->carrier_fee) / $sameAddressCount, 2);
                    } else {
                        $item->calculated_carrier_fee = (float) $item->carrier_fee;
                    }

                    $allDetails[] = $item;
                }
            }

            // Summarize the day
            $dayCarrierFee = (float) $dayItems->sum('calculated_carrier_fee');
            $dayKm = (float) $dayItems->sum('calculated_km');
            $dayWeight = (float) $dayItems->sum('weight_kg');

            $grandTotalWeight += $dayWeight;
            $grandTotalCarrierFee += $dayCarrierFee;
            $grandTotalKm += $dayKm;

            $packageStrings = $dayItems->pluck('package_count')->filter()->unique()->values()->all();

            $dailySummaries[] = (object) [
                'date' => $firstItem->delivery_date,
                'date_formatted' => $firstItem->delivery_date ? $firstItem->delivery_date->format('d/m/Y') : '—',
                'employee_id' => $firstItem->employee_id,
                'employee_name' => $firstItem->employee?->name ?? '—',
                'is_support' => $isSupport,
                'km_deduction' => $kmDau,
                'trip_count' => $dayTripsCount,
                'order_count' => $dayItems->count(),
                'package_count_text' => !empty($packageStrings) ? implode(', ', $packageStrings) : '—',
                'total_weight_kg' => $dayWeight,
                'total_carrier_fee' => $dayCarrierFee,
                'total_distance_km' => $dayKm,
                'raw_distance_km' => (float) $dayItems->sum('distance_km'),
                'raw_carrier_fee' => (float) $dayItems->sum('carrier_fee'),
                'motorbike_driver' => $dayItems->pluck('motorbike_driver')->filter()->unique()->implode(', ') ?: '—',
                'items' => $dayItems,
            ];
        }

        return [
            'daily_summaries' => $dailySummaries,
            'all_details' => $allDetails,
            'totals' => [
                'total_days' => count($dailySummaries),
                'total_trips' => $grandTotalTrips,
                'total_orders' => $grandTotalOrders,
                'total_weight_kg' => $grandTotalWeight,
                'total_carrier_fee' => $grandTotalCarrierFee,
                'total_distance_km' => round($grandTotalKm, 2),
            ],
        ];
    }

    /**
     * Fallback CSV export with UTF-8 BOM when ZipArchive extension is not enabled.
     */
    private function exportCsvFallback(string $mode, array $calculationResult)
    {
        $filename = ($mode === 'daily' ? 'BANG_TINH_KILOMET_THEO_NGAY_' : 'BANG_TINH_CHANH_VA_KILOMET_') . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($mode, $calculationResult) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF)); // Write UTF-8 BOM

            if ($mode === 'daily') {
                fputcsv($handle, [
                    'STT', 'NGÀY', 'TÊN CHỞ HÀNG', 'LOẠI NHÂN VIÊN', 'SỐ CHUYẾN',
                    'SỐ ĐIỂM GIAO', 'SỐ BAO', 'SỐ KG', 'TIỀN CHÀNH (VNĐ)', 'TỔNG KM TÍNH TIỀN', 'XE ÔM'
                ]);

                $stt = 1;
                foreach ($calculationResult['daily_summaries'] as $day) {
                    fputcsv($handle, [
                        $stt++,
                        $day->date_formatted,
                        $day->employee_name,
                        $day->is_support ? 'Hỗ trợ (0km đầu)' : 'Thường (trừ 2km đầu)',
                        $day->trip_count,
                        $day->order_count,
                        $day->package_count_text,
                        $day->total_weight_kg,
                        $day->total_carrier_fee,
                        $day->total_distance_km,
                        $day->motorbike_driver,
                    ]);
                }

                fputcsv($handle, [
                    'TỔNG CỘNG', $calculationResult['totals']['total_days'] . ' ngày', '', '',
                    $calculationResult['totals']['total_trips'] . ' chuyến',
                    $calculationResult['totals']['total_orders'] . ' điểm', '',
                    $calculationResult['totals']['total_weight_kg'],
                    $calculationResult['totals']['total_carrier_fee'],
                    $calculationResult['totals']['total_distance_km'], ''
                ]);
            } else {
                fputcsv($handle, [
                    'NGÀY', 'NHÓM', 'CHÀNH XE - CỬA HÀNG', 'ĐỊA CHỈ', 'PHƯỜNG', 'QUẬN',
                    'KM GỐC', 'TIỀN CHÀNH GỐC', 'TÊN CHỞ HÀNG', 'SỐ CHUYẾN', 'SỐ BAO', 'SỐ KG',
                    'TIỀN CHÀNH TÍNH', 'TỔNG KM TÍNH', 'XE ÔM', 'THÔNG TIN GHI BAO'
                ]);

                foreach ($calculationResult['all_details'] as $item) {
                    fputcsv($handle, [
                        $item->delivery_date ? $item->delivery_date->format('d/m/Y') : '',
                        $item->group_name,
                        $item->invoice_name,
                        $item->address,
                        $item->ward,
                        $item->district,
                        $item->distance_km,
                        $item->carrier_fee,
                        $item->employee?->name ?? '',
                        $item->trip_count,
                        $item->package_count,
                        $item->weight_kg,
                        $item->calculated_carrier_fee,
                        $item->calculated_km,
                        $item->motorbike_driver,
                        $item->package_note,
                    ]);
                }
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
