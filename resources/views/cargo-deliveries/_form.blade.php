@php
    $delivery = $delivery ?? null;
    $isEditing = $isEditing ?? (!empty($delivery?->id));
    $formAction = $formAction ?? route('cargo-deliveries.store');
    $formMethod = $formMethod ?? 'POST';
@endphp

<form method="POST" action="{{ $formAction }}" class="cargo-delivery-form">
    @csrf
    @if ($formMethod !== 'POST') @method($formMethod) @endif

    <div class="cargo-form-top-grid">
        <div class="cargo-form-field">
            <label for="delivery-date-{{ $delivery?->id ?? 'new' }}">Ngày</label>
            @if ($isEditing)
                <input id="delivery-date-{{ $delivery->id }}" type="text" value="{{ $delivery->delivery_date?->format('d/m/Y H:i') }}" readonly disabled style="background-color: #f1f5f9; cursor: not-allowed; color: #475569; font-weight: 600;" tabindex="-1">
            @else
                <input id="delivery-date-new" name="delivery_date" type="date" value="{{ old('delivery_date', now()->format('Y-m-d')) }}" required>
            @endif
        </div>
        <div class="cargo-form-field">
            <label for="employee-{{ $delivery?->id ?? 'new' }}">Tên chở hàng</label>
            <select id="employee-{{ $delivery?->id ?? 'new' }}" name="employee_id" required>
                <option value="">Chọn nhân viên</option>
                @foreach ($employees as $employee)
                    <option value="{{ $employee->id }}" @selected($isEditing && (string) old('employee_id', $delivery?->employee_id) === (string) $employee->id)>{{ $employee->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="cargo-form-field">
            <label for="trip-count-{{ $delivery?->id ?? 'new' }}">Số chuyến</label>
            <input id="trip-count-{{ $delivery?->id ?? 'new' }}" name="trip_count" type="number" min="1" value="{{ $isEditing ? old('trip_count', $delivery?->trip_count ?? 1) : old('trip_count', 1) }}" required>
        </div>
        <div class="cargo-form-field">
            <label for="package-count-{{ $delivery?->id ?? 'new' }}">Số bao</label>
            <input id="package-count-{{ $delivery?->id ?? 'new' }}" name="package_count" type="text" value="{{ $isEditing ? old('package_count', $delivery?->package_count) : old('package_count', '') }}" required placeholder="Số bao">
        </div>
        <div class="cargo-form-field">
            <label for="weight-kg-{{ $delivery?->id ?? 'new' }}">Số kg</label>
            <input id="weight-kg-{{ $delivery?->id ?? 'new' }}" name="weight_kg" type="number" min="0" step="0.01" value="{{ $isEditing ? old('weight_kg', $delivery?->weight_kg) : old('weight_kg', '') }}" required placeholder="0.00">
        </div>
        <div class="cargo-form-field cargo-form-field-wide">
            <label for="delivery-note-{{ $delivery?->id ?? 'new' }}">Ghi chú</label>
            <textarea id="delivery-note-{{ $delivery?->id ?? 'new' }}" name="note" rows="2" placeholder="Nhập ghi chú">{{ $isEditing ? old('note', $delivery?->note) : old('note', '') }}</textarea>
        </div>
    </div>

    <div class="cargo-selection-section">
        <div class="cargo-selection-heading">
            <div><span class="panel-kicker">Active group source</span><h3>THÔNG TIN DANH SÁCH GIAO HÀNG</h3></div>
            <input type="search" class="cargo-kilometer-search" placeholder="Tìm tên xuất phiếu, nhóm, địa chỉ, quận, ghi bao..." data-cargo-kilometer-search>
        </div>
        <div class="cargo-kilometer-table-wrap">
            <div class="cargo-kilometer-scroll-pane">
                <div class="cargo-kilometer-header">
                    <span>CHỌN</span>
                    <span>NGÀY</span>
                    <span>TÊN XUẤT PHIẾU</span>
                    <span>NHÓM</span>
                    <span>ĐỊA CHỈ</span>
                    <span>PHƯỜNG</span>
                    <span>QUẬN</span>
                    <span>SỐ KM</span>
                    <span>XE ÔM</span>
                    <span>THÔNG TIN GHI BAO</span>
                </div>
                <div class="cargo-kilometer-list">
                    @php
                        $removeAccents = static function (?string $str): string {
                            if (! $str) return '';
                            $str = preg_replace('/[àáạảãâầấậẩẫăằắặẳẵ]/u', 'a', $str);
                            $str = preg_replace('/[èéẹẻẽêềếệểễ]/u', 'e', $str);
                            $str = preg_replace('/[ìíịỉĩ]/u', 'i', $str);
                            $str = preg_replace('/[òóọỏõôồốộổỗơờớợởỡ]/u', 'o', $str);
                            $str = preg_replace('/[ùúụủũưừứựửữ]/u', 'u', $str);
                            $str = preg_replace('/[ỳýỵỷỹ]/u', 'y', $str);
                            $str = preg_replace('/[đ]/u', 'd', $str);
                            return $str;
                        };
                    @endphp
                    @foreach ($groups as $groupOption)
                        @php
                            $searchParts = array_filter([
                                $groupOption->invoice_name,
                                $groupOption->group_name,
                                $groupOption->address,
                                $groupOption->ward,
                                $groupOption->district,
                                $groupOption->package_note,
                            ]);
                            if (!empty($groupOption->district)) {
                                $searchParts[] = 'quận ' . $groupOption->district;
                                $searchParts[] = 'quan ' . $groupOption->district;
                            }
                            if (!empty($groupOption->ward)) {
                                $searchParts[] = 'phường ' . $groupOption->ward;
                                $searchParts[] = 'phuong ' . $groupOption->ward;
                            }
                            $combined = implode(' ', $searchParts);
                            $lower = mb_strtolower($combined, 'UTF-8');
                            $unaccented = $removeAccents($lower);
                            $noSpaces = str_replace(' ', '', $unaccented);
                            $fullSearchData = $lower . ' ' . $unaccented . ' ' . $noSpaces;
                        @endphp
                        <label class="cargo-kilometer-option" data-search="{{ $fullSearchData }}">
                            <input type="radio" name="group_id" value="{{ $groupOption->id }}" @checked($isEditing && (string) old('group_id', $delivery?->group_id) === (string) $groupOption->id) required>
                            <span>{{ $groupOption->group_date->format('d/m/Y H:i') }}</span>
                            <strong class="cargo-option-invoice" title="{{ $groupOption->invoice_name }}">{{ $groupOption->invoice_name ?: '—' }}</strong>
                            <span class="group-badge cargo-option-group">{{ $groupOption->group_name }}</span>
                            <span class="cargo-option-address" title="{{ $groupOption->address }}">{{ $groupOption->address }}</span>
                            <span>{{ $groupOption->ward }}</span>
                            <span>{{ $groupOption->district }}</span>
                            <span>{{ number_format((float) $groupOption->distance_km, 2, ',', '.') }} km</span>
                            <span>{{ $groupOption->motorbike_driver ?: '—' }}</span>
                            <span class="cargo-option-note" title="{{ $groupOption->package_note }}">{{ $groupOption->package_note ?: '—' }}</span>
                        </label>
                    @endforeach
                    <div class="cargo-kilometer-empty" style="display: none; padding: 1.5rem; text-align: center; color: var(--muted); font-size: .8rem;">Không tìm thấy thông tin giao hàng phù hợp.</div>
                </div>
            </div>
        </div>
    </div>

    <div class="cargo-form-alert alert" style="display: none; margin-top: 1.25rem; margin-bottom: 0;" role="alert"></div>

    <div class="cargo-modal-actions">
        <button type="button" class="btn kilometer-cancel-button" data-bs-dismiss="modal">Đóng</button>
        <button type="submit" class="btn kilometer-submit-button">{{ $isEditing ? 'Lưu thay đổi' : 'Lưu thông tin' }}</button>
    </div>
</form>
