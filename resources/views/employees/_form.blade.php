@php
    $employee = $employee ?? null;
    $isEditing = $employee !== null;
    $formAction = $formAction ?? route('employees.store');
    $formMethod = $formMethod ?? 'POST';
@endphp

<form method="POST" action="{{ $formAction }}" class="employee-form">
    @csrf
    @if ($formMethod !== 'POST')
        @method($formMethod)
    @endif

    <div class="employee-form-grid">
        <div class="employee-thumbnail-field">
            <span>Thumbnail</span>
            <img class="employee-form-preview" src="{{ asset('images/default-avatar.svg') }}" data-default-thumbnail="{{ asset('images/default-avatar.svg') }}" alt="Ảnh đại diện mặc định">
            <small>Ảnh mặc định của nhân viên.</small>
        </div>

        <div class="employee-form-fields">
            <div class="employee-form-field">
                <label for="employee-name-{{ $employee?->id ?? 'new' }}">Tên nhân viên</label>
                <input id="employee-name-{{ $employee?->id ?? 'new' }}" name="name" type="text" value="{{ old('name', $employee?->name) }}" required placeholder="Nhập tên nhân viên">
            </div>
            <div class="employee-form-field">
                <label for="started-at-{{ $employee?->id ?? 'new' }}">Ngày vào làm</label>
                <input id="started-at-{{ $employee?->id ?? 'new' }}" name="started_at" type="date" value="{{ $isEditing ? old('started_at', $employee->started_at?->format('Y-m-d')) : old('started_at', now()->format('Y-m-d')) }}" required>
            </div>
            <div class="employee-form-field">
                <label for="employee-address-{{ $employee?->id ?? 'new' }}">Địa chỉ</label>
                <textarea id="employee-address-{{ $employee?->id ?? 'new' }}" name="address" rows="3" required placeholder="Nhập địa chỉ">{{ old('address', $employee?->address) }}</textarea>
            </div>
            <div class="employee-form-field employee-form-field-checkbox">
                <label class="employee-checkbox-label" for="employee-support-{{ $employee?->id ?? 'new' }}">
                    <input id="employee-support-{{ $employee?->id ?? 'new' }}" name="is_support" type="checkbox" value="1" @checked($isEditing ? old('is_support', $employee?->is_support) : old('is_support', false))>
                    <span>Hỗ trợ</span>
                </label>
                <small class="group-form-help">Đánh dấu nếu nhân viên này thuộc bộ phận / vai trò hỗ trợ.</small>
            </div>
        </div>
    </div>

    <div class="employee-modal-actions">
        <button type="button" class="btn kilometer-cancel-button" data-bs-dismiss="modal">Đóng</button>
        <button type="submit" class="btn kilometer-submit-button">{{ $isEditing ? 'Lưu thay đổi' : 'Thêm mới' }}</button>
    </div>
</form>
