@php
    $delivery = $delivery ?? null;
    $isEditing = $isEditing ?? (!empty($delivery?->id));
    $formAction = $formAction ?? route('cargo-deliveries.store');
    $formMethod = $formMethod ?? 'POST';
    $fieldSuffix = $delivery?->id ?? ($isEditing ? 'edit' : 'new');
@endphp

<form method="POST" action="{{ $formAction }}" class="cargo-delivery-form">
    @csrf
    @if ($formMethod !== 'POST') @method($formMethod) @endif

    <div class="cargo-form-top-grid">
        <div class="cargo-form-field">
            <label for="delivery-date-{{ $fieldSuffix }}">Ngày</label>
            @if ($isEditing)
                <input id="delivery-date-{{ $fieldSuffix }}" type="text" value="{{ $delivery?->delivery_date?->format('d/m/Y H:i') }}" readonly disabled style="background-color: #f1f5f9; cursor: not-allowed; color: #475569; font-weight: 600;" tabindex="-1">
            @else
                <input id="delivery-date-new" name="delivery_date" type="date" value="{{ old('delivery_date', now()->format('Y-m-d')) }}" required>
            @endif
        </div>
        <div class="cargo-form-field">
            <label for="employee-{{ $fieldSuffix }}">Tên chở hàng</label>
            <select id="employee-{{ $fieldSuffix }}" name="employee_id" required>
                <option value="">Chọn nhân viên</option>
                @foreach ($employees as $employee)
                    <option value="{{ $employee->id }}" @selected($isEditing && (string) old('employee_id', $delivery?->employee_id) === (string) $employee->id)>{{ $employee->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="cargo-form-field">
            <label for="trip-count-{{ $fieldSuffix }}">Số chuyến</label>
            <input id="trip-count-{{ $fieldSuffix }}" name="trip_count" type="number" min="1" value="{{ $isEditing ? old('trip_count', $delivery?->trip_count ?? 1) : old('trip_count', 1) }}" required>
        </div>
        <div class="cargo-form-field">
            <label for="package-count-{{ $fieldSuffix }}">Số bao</label>
            <input id="package-count-{{ $fieldSuffix }}" name="package_count" type="text" value="{{ $isEditing ? old('package_count', $delivery?->package_count) : old('package_count', '') }}" required placeholder="Số bao">
        </div>
        <div class="cargo-form-field">
            <label for="weight-kg-{{ $fieldSuffix }}">Số kg</label>
            <input id="weight-kg-{{ $fieldSuffix }}" name="weight_kg" type="number" min="0" step="0.01" value="{{ $isEditing ? old('weight_kg', $delivery?->weight_kg) : old('weight_kg', '') }}" required placeholder="0.00">
        </div>
        <div class="cargo-form-field cargo-form-field-wide">
            <label for="delivery-note-{{ $fieldSuffix }}">Ghi chú</label>
            <textarea id="delivery-note-{{ $fieldSuffix }}" name="note" rows="2" placeholder="Nhập ghi chú">{{ $isEditing ? old('note', $delivery?->note) : old('note', '') }}</textarea>
        </div>
    </div>

    <div class="cargo-selection-section">
        <div class="cargo-selection-heading">
            <div><span class="panel-kicker">Active group source</span><h3>THÔNG TIN DANH SÁCH GIAO HÀNG</h3></div>
            <div class="cargo-group-searches">
                <input type="search" class="cargo-kilometer-search" placeholder="Tìm tên xuất phiếu, địa chỉ, quận, ghi bao..." data-cargo-kilometer-search autocomplete="off">
                <input type="search" class="cargo-kilometer-search cargo-group-search-input" placeholder="Tìm theo nhóm..." data-cargo-group-search autocomplete="off" aria-label="Tìm theo nhóm">
            </div>
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
                    @foreach ($groups as $groupOption)
                        <label class="cargo-kilometer-option" data-search="{{ $groupOption->search_key }}" data-group-name="{{ $groupOption->group_name }}">
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
