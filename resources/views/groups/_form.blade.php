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
            <div class="group-invoice-combobox" data-group-invoice-combobox>
                <input
                    id="group-invoice-{{ $group?->id ?? 'new' }}"
                    name="invoice_name"
                    type="text"
                    value="{{ old('invoice_name', $group?->invoice_name) }}"
                    required
                    placeholder="Tìm tên đã có hoặc nhập tên xuất phiếu mới"
                    autocomplete="off"
                    role="combobox"
                    aria-autocomplete="list"
                    aria-expanded="false"
                    aria-controls="group-invoice-options-{{ $group?->id ?? 'new' }}"
                    data-group-invoice-input
                >
                <div
                    id="group-invoice-options-{{ $group?->id ?? 'new' }}"
                    class="group-invoice-options"
                    data-group-invoice-options
                    role="listbox"
                    hidden
                >
                    @foreach ($invoiceSuggestions as $suggestion)
                        @php
                            $suggestionKilometer = $suggestion->kilometer;
                            $kilometerLabel = $suggestionKilometer
                                ? implode(' · ', array_filter([
                                    $suggestionKilometer->address,
                                    $suggestionKilometer->ward,
                                    $suggestionKilometer->district,
                                    number_format((float) $suggestionKilometer->distance_km, 2, ',', '.') . ' km',
                                    number_format((float) $suggestionKilometer->carrier_fee, 0, ',', '.') . ' đ',
                                    $suggestionKilometer->package_note ? 'Ghi bao: ' . $suggestionKilometer->package_note : null,
                                ]))
                                : '';
                            $suggestionParts = array_filter([
                                $suggestion->address,
                                'Phường ' . $suggestion->ward,
                                'Quận ' . $suggestion->district,
                                number_format((float) $suggestion->distance_km, 2, ',', '.') . ' km',
                                'Tiền chành ' . number_format((float) $suggestion->carrier_fee, 0, ',', '.') . ' đ',
                                'Ghi bao: ' . ($suggestion->package_note ?: '—'),
                            ]);
                        @endphp
                        <button
                            type="button"
                            class="group-invoice-option"
                            role="option"
                            data-invoice="{{ $suggestion->invoice_name }}"
                            data-kilometer-id="{{ $suggestion->kilometer_id }}"
                            data-group-name="{{ $suggestion->group_name }}"
                            data-search="{{ $suggestion->invoice_name }}"
                            data-label="{{ $kilometerLabel }}"
                        >
                            <strong>{{ $suggestion->invoice_name }}</strong>
                            <span class="group-badge">{{ $suggestion->group_name }}</span>
                            <small>{{ implode(' · ', $suggestionParts) }}</small>
                        </button>
                    @endforeach
                    <p class="group-invoice-empty" data-group-invoice-empty hidden>
                        Chưa có tên này trong nhóm. Bạn có thể nhập tên mới rồi chọn thông tin địa chỉ / kilomet bên dưới.
                    </p>
                </div>
            </div>
            <small class="group-form-help">Chọn tên có sẵn để tự lấy địa chỉ / kilomet tương ứng, hoặc nhập tên xuất phiếu mới.</small>
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
                            $searchParts = array_filter([
                                $kilometer->address,
                                $kilometer->ward,
                                'phường ' . $kilometer->ward,
                                $kilometer->district,
                                'quận ' . $kilometer->district,
                                (string) $kilometer->distance_km,
                                number_format((float) $kilometer->distance_km, 2, ',', '.'),
                                'km ' . number_format((float) $kilometer->distance_km, 2, ',', '.'),
                                $kilometer->carrier_fee,
                                'tiền chành ' . number_format((float) $kilometer->carrier_fee, 0, ',', '.'),
                                $kilometer->package_note,
                                'ghi bao ' . $kilometer->package_note,
                            ]);
                            $optionSearch = implode(' ', $searchParts);
                        @endphp
                        <option value="{{ $kilometer->id }}" data-label="{{ $optionLabel }}" data-search="{{ $optionSearch }}" @selected((string) old('kilometer_id', $group?->kilometer_id) === (string) $kilometer->id)>{{ $optionLabel }}</option>
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
