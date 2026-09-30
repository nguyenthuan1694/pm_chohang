@php
    $group = $group ?? null;
    $isEditing = $group !== null;
    $formAction = $formAction ?? route('groups.store');
    $formMethod = $formMethod ?? 'POST';
    $user = auth()->user();
    $displayGroupName = $group?->group_name;
    if (!$displayGroupName && $user?->role === 'group') {
        $displayGroupName = $user->group_id ? ('Nhóm ' . $user->group_id) : ($user->name ?: 'Nhóm kinh doanh');
    }
@endphp

<form method="POST" action="{{ $formAction }}" class="group-form">
    @csrf
    @if ($formMethod !== 'POST') @method($formMethod) @endif

    <div class="group-form-grid">
        <div class="group-form-field group-form-field-wide">
            <label for="group-team-{{ $group?->id ?? 'new' }}">Nhóm</label>
            @if ($user?->role === 'group')
                <input id="group-team-{{ $group?->id ?? 'new' }}" type="text" value="{{ $displayGroupName }}" readonly style="background-color: #f7faf7; font-weight: 700; color: var(--mint-deep, #246b4b);">
                <small class="group-form-help">Tự động lấy theo tài khoản Nhóm kinh doanh đang đăng nhập ({{ $displayGroupName }}).</small>
            @else
                <input id="group-team-{{ $group?->id ?? 'new' }}" name="group_name" type="text" value="{{ old('group_name', $group?->group_name) }}" placeholder="Tự động lấy theo kilomet đã chọn nếu để trống">
                <small class="group-form-help">Nếu để trống, nhóm sẽ được lấy theo dòng kilomet đã chọn.</small>
            @endif
        </div>
        <div class="group-form-field group-form-field-wide">
            <label for="group-invoice-{{ $group?->id ?? 'new' }}">Tên xuất phiếu</label>
            <input id="group-invoice-{{ $group?->id ?? 'new' }}" name="invoice_name" type="text" value="{{ old('invoice_name', $group?->invoice_name) }}" required placeholder="Nhập tên xuất phiếu">
        </div>
        <div class="group-form-field group-form-field-wide">
            <label for="group-kilometer-{{ $group?->id ?? 'new' }}">Thông tin địa chỉ / kilomet</label>
            <div class="group-kilometer-combobox" data-group-kilometer-combobox>
                <input type="text" class="group-kilometer-combobox-input" data-group-kilometer-input placeholder="Gõ để tìm và chọn thông tin kilomet..." autocomplete="off" role="combobox" aria-expanded="false" aria-controls="group-kilometer-options-{{ $group?->id ?? 'new' }}">
                <select id="group-kilometer-{{ $group?->id ?? 'new' }}" name="kilometer_id" class="group-kilometer-native-select" required aria-hidden="true" tabindex="-1">
                    <option value="">Chọn thông tin kilomet</option>
                    @foreach ($kilometers as $kilometer)
                        @php
                            $parts = [
                                $kilometer->address,
                                $kilometer->ward,
                                $kilometer->district,
                                number_format((float) $kilometer->distance_km, 2, ',', '.') . ' km',
                                number_format((float) $kilometer->carrier_fee, 0, ',', '.') . ' đ',
                            ];
                            if (!empty($kilometer->package_note)) {
                                $parts[] = 'Ghi bao: ' . $kilometer->package_note;
                            }
                            $optionLabel = implode(' · ', $parts);
                        @endphp
                        <option value="{{ $kilometer->id }}" data-label="{{ $optionLabel }}" data-search="{{ strtolower($optionLabel) }}" @selected((string) old('kilometer_id', $group?->kilometer_id) === (string) $kilometer->id)>{{ $optionLabel }}</option>
                    @endforeach
                </select>
                <div id="group-kilometer-options-{{ $group?->id ?? 'new' }}" class="group-kilometer-options" data-group-kilometer-options></div>
            </div>
            <small class="group-form-help">Địa chỉ, Phường, Quận, KM, Tiền chành, Xe ôm và Thông tin ghi bao được tự động lấy theo dòng kilomet đã chọn.</small>
        </div>
    </div>

    <div class="group-modal-actions">
        <button type="button" class="btn kilometer-cancel-button" data-bs-dismiss="modal">Đóng</button>
        <button type="submit" class="btn kilometer-submit-button">{{ $isEditing ? 'Lưu thay đổi' : 'Thêm mới' }}</button>
    </div>
</form>
