@php
    $kilometer = $kilometer ?? null;
    $isEditing = $kilometer !== null;
    $showCreateCopyButton = $showCreateCopyButton ?? false;
    $formAction = $formAction ?? route('kilometers.store');
    $formMethod = $formMethod ?? 'POST';
    $fieldValue = fn (string $field, mixed $default = '') => $isEditing
        ? old($field, $kilometer->{$field})
        : old($field, $default);
@endphp

<form method="POST" action="{{ $formAction }}" class="kilometer-form">
    @csrf
    @if ($formMethod !== 'POST')
        @method($formMethod)
    @endif

    <div class="kilometer-form-grid">
        <div class="kilometer-form-field">
            <label for="date-{{ $kilometer?->id ?? 'new' }}">Ngày</label>
            <input id="date-{{ $kilometer?->id ?? 'new' }}" name="date" type="date" value="{{ $isEditing ? old('date', $kilometer->date?->format('Y-m-d')) : old('date', now()->format('Y-m-d')) }}" required>
        </div>

        <div class="kilometer-form-field">
            <label for="address-{{ $kilometer?->id ?? 'new' }}">Địa chỉ</label>
            <input id="address-{{ $kilometer?->id ?? 'new' }}" name="address" type="text" value="{{ $fieldValue('address') }}" required placeholder="Nhập địa chỉ">
        </div>

        <div class="kilometer-form-field">
            <label for="ward-{{ $kilometer?->id ?? 'new' }}">Phường</label>
            <input id="ward-{{ $kilometer?->id ?? 'new' }}" name="ward" type="text" value="{{ $fieldValue('ward') }}" required placeholder="Nhập phường">
        </div>

        <div class="kilometer-form-field">
            <label for="district-{{ $kilometer?->id ?? 'new' }}">Quận/huyện</label>
            <input id="district-{{ $kilometer?->id ?? 'new' }}" name="district" type="text" value="{{ $fieldValue('district') }}" required placeholder="Nhập quận/huyện">
        </div>

        <div class="kilometer-form-field">
            <label for="distance-km-{{ $kilometer?->id ?? 'new' }}">KM</label>
            <input id="distance-km-{{ $kilometer?->id ?? 'new' }}" name="distance_km" type="number" min="0" step="0.01" value="{{ $fieldValue('distance_km') }}" required placeholder="0.00">
        </div>

        <div class="kilometer-form-field">
            <label for="carrier-fee-{{ $kilometer?->id ?? 'new' }}">Tiền chành</label>
            <input id="carrier-fee-{{ $kilometer?->id ?? 'new' }}" name="carrier_fee" type="number" min="0" step="1000" value="{{ $fieldValue('carrier_fee') }}" required placeholder="0">
        </div>

        <div class="kilometer-form-field">
            <label for="motorbike-driver-{{ $kilometer?->id ?? 'new' }}">Xe ôm</label>
            <input id="motorbike-driver-{{ $kilometer?->id ?? 'new' }}" name="motorbike_driver" type="text" value="{{ $fieldValue('motorbike_driver') }}" placeholder="Tên tài xế">
        </div>

        <div class="cargo-form-field cargo-form-field-wide">
            <label for="package-note-{{ $kilometer?->id ?? 'new' }}">Thông tin ghi bao</label>
            <textarea id="package-note-{{ $kilometer?->id ?? 'new' }}" name="package_note" rows="3" placeholder="Nhập ghi chú nếu có">{{ $fieldValue('package_note') }}</textarea>
        </div>
    </div>

    <div class="kilometer-modal-actions">
        @if ($showCreateCopyButton)
            <button
                type="button"
                class="btn kilometer-submit-button"
                data-kilometer-create-copy
                data-create-action="{{ route('kilometers.store') }}"
                data-system-date="{{ now()->format('Y-m-d') }}"
            >Th&#234;m m&#7899;i kilomet</button>
        @endif
        <button type="button" class="btn kilometer-cancel-button" data-bs-dismiss="modal">Đóng</button>
        <button type="submit" class="btn kilometer-submit-button">{{ $kilometer ? 'Lưu thay đổi' : 'Thêm mới' }}</button>
    </div>
</form>
