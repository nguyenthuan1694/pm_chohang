@extends('layouts.dashboard')

@section('title', 'DS Nhóm | ' . config('app.name', 'Laravel'))

@section('content')
<div class="dashboard-overview group-page">
    @if (session('status'))<div class="alert alert-success dashboard-alert" role="alert">{{ session('status') }}</div>@endif

    <div class="page-heading-row">
        <div><p class="dashboard-breadcrumb">Workspace / Groups</p><h1 class="page-heading">D.Sách thông tin giao hàng của nhóm</h1><p class="page-subtitle">Quản lý thông tin xuất phiếu và tuyến giao hàng theo nhóm.</p></div>
        <button type="button" class="btn kilometer-add-button" data-bs-toggle="modal" data-bs-target="#createGroupModal"><span aria-hidden="true">+</span> Thêm thông tin giao hàng</button>
    </div>

    <section class="kilometer-toolbar">
        <form method="GET" action="{{ route('groups.index') }}" class="kilometer-search-form"><label class="visually-hidden" for="group-search">Tìm kiếm nhóm</label><span class="search-icon" aria-hidden="true">⌕</span><input id="group-search" name="search" type="search" value="{{ $search }}" placeholder="Tìm tên phiếu, nhóm, địa chỉ...">@if ($search !== '')<a class="clear-search" href="{{ route('groups.index') }}">×</a>@endif<button type="submit" class="kilometer-search-button">Tìm kiếm</button></form>
        <span class="kilometer-updated">Cập nhật {{ now()->format('d/m/Y') }}</span>
    </section>

    <section class="kilometer-table-card">
        <div class="kilometer-table-wrap">
            <table class="kilometer-table group-table">
                <thead><tr><th>NGÀY</th><th>TÊN XUẤT PHIẾU</th><th>NHÓM</th><th>ĐỊA CHỈ</th><th>PHƯỜNG</th><th>QUẬN</th><th>KM</th><th>TIỀN CHÀNH</th><th>XE ÔM</th><th>THÔNG TIN GHI BAO</th><th>TRẠNG THÁI</th><th>TÁC VỤ</th></tr></thead>
                <tbody>
                    @forelse ($groups as $group)
                        <tr>
                            <td class="date-cell">{{ $group->group_date->format('d/m/Y H:i') }}</td><td><strong>{{ $group->invoice_name }}</strong></td><td><span class="group-badge">{{ $group->group_name }}</span></td><td class="address-cell">{{ $group->address }}</td><td>{{ $group->ward }}</td><td>{{ $group->district }}</td><td class="number-cell">{{ number_format((float) $group->distance_km, 2, ',', '.') }}</td><td class="money-cell">{{ number_format((float) $group->carrier_fee, 0, ',', '.') }} đ</td><td>{{ $group->motorbike_driver ?: '—' }}</td><td class="note-cell">{{ $group->package_note ?: '—' }}</td>
                            <td><form method="POST" action="{{ route('groups.status', $group) }}">@csrf @method('PATCH')<select name="status" class="group-status-select status-{{ $group->status }}" onchange="this.form.submit()" @disabled($group->status === 'active')><option value="inactive" @selected($group->status === 'inactive')>Chưa Active</option><option value="active" @selected($group->status === 'active')>Active</option></select></form></td>
                            <td><div class="kilometer-actions"><button type="button" class="kilometer-action-button edit" data-bs-toggle="modal" data-bs-target="#editGroupModal-{{ $group->id }}">Sửa</button> @if($group->status === 'inactive')<form method="POST" action="{{ route('groups.destroy', $group) }}" onsubmit="return confirm('Bạn có chắc muốn xóa nhóm này?');">@csrf @method('DELETE')<button type="submit" class="kilometer-action-button delete">Xóa</button></form>@endif</div></td>
                        </tr>
                    @empty
                        <tr><td colspan="12" class="empty-table-cell"><strong>Chưa có dữ liệu nhóm</strong><span>{{ $search !== '' ? 'Không tìm thấy nhóm phù hợp.' : 'Dữ liệu sẽ hiển thị sau khi được thêm.' }}</span></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($groups->hasPages())<div class="kilometer-pagination">{{ $groups->links() }}</div>@endif
    </section>
</div>

<div class="modal fade" id="createGroupModal" tabindex="-1" aria-labelledby="createGroupModalLabel" aria-hidden="true"><div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content kilometer-modal-content"><div class="modal-header"><div><span class="panel-kicker">New delivery information</span><h2 class="modal-title" id="createGroupModalLabel">Thêm thông tin giao hàng</h2></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button></div><div class="modal-body">@include('groups._form', ['group' => null, 'isEditing' => false])</div></div></div></div>

@foreach ($groups as $group)
    <div class="modal fade" id="editGroupModal-{{ $group->id }}" tabindex="-1" aria-labelledby="editGroupModalLabel-{{ $group->id }}" aria-hidden="true"><div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content kilometer-modal-content"><div class="modal-header"><div><span class="panel-kicker">Edit group</span><h2 class="modal-title" id="editGroupModalLabel-{{ $group->id }}">Sửa nhóm</h2></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button></div><div class="modal-body">@include('groups._form', ['group' => $group, 'formAction' => route('groups.update', $group), 'formMethod' => 'PUT'])</div></div></div></div>
@endforeach

<style>
.group-invoice-combobox { position: relative; }
.group-invoice-options {
    position: absolute;
    inset: calc(100% + .25rem) 0 auto;
    z-index: 1060;
    max-height: 280px;
    overflow-y: auto;
    padding: .35rem;
    background: #fff;
    border: 1px solid var(--line);
    border-radius: 8px;
    box-shadow: 0 12px 28px rgba(20, 45, 32, .16);
}
.group-invoice-options[hidden],
.group-invoice-option[hidden],
.group-invoice-empty[hidden] { display: none !important; }
.group-invoice-option {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: .3rem .7rem;
    width: 100%;
    padding: .65rem .75rem;
    text-align: left;
    color: var(--ink);
    background: #fff;
    border: 0;
    border-bottom: 1px solid #edf1ed;
    cursor: pointer;
}
.group-invoice-option:hover,
.group-invoice-option:focus { background: #eaf5ec; outline: 0; }
.group-invoice-option small { grid-column: 1 / -1; color: var(--muted); font-size: .72rem; }
.group-invoice-empty { margin: 0; padding: .8rem; color: var(--muted); font-size: .78rem; }
</style>

<script>
(function() {
    const normalize = function(value) {
        return (value || '').toString().toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/đ/g, 'd')
            .replace(/[^\w\s]/g, ' ')
            .trim();
    };

    const selectInvoiceOption = function(form, option) {
        const invoiceInput = form.querySelector('[data-group-invoice-input]');
        const kilometerSelect = form.querySelector('select[name="kilometer_id"]');
        const kilometerInput = form.querySelector('[data-group-kilometer-input]');
        if (!invoiceInput || !kilometerSelect || !kilometerInput) return;

        const kilometerOption = [...kilometerSelect.options].find(function(item) {
            return item.value === option.dataset.kilometerId;
        });
        if (!kilometerOption) return;

        invoiceInput.value = option.dataset.invoice || '';
        invoiceInput.setAttribute('aria-expanded', 'false');
        form.dataset.selectedInvoice = invoiceInput.value;
        kilometerSelect.value = kilometerOption.value;
        kilometerInput.value = option.dataset.label || kilometerOption.dataset.label || '';

        const groupNameInput = form.querySelector('input[name="group_name"]');
        if (groupNameInput && !groupNameInput.readOnly) {
            groupNameInput.value = option.dataset.groupName || '';
        }

        const kilometerOptions = form.querySelector('[data-group-kilometer-options]');
        if (kilometerOptions) kilometerOptions.hidden = true;
        kilometerInput.setAttribute('aria-expanded', 'false');
    };

    document.addEventListener('input', function(event) {
        const invoiceInput = event.target;
        if (!(invoiceInput instanceof HTMLInputElement) || !invoiceInput.matches('[data-group-invoice-input]')) return;

        const form = invoiceInput.closest('.group-form');
        const optionsList = invoiceInput.parentElement.querySelector('[data-group-invoice-options]');
        if (!form || !optionsList) return;

        if (form.dataset.selectedInvoice && normalize(form.dataset.selectedInvoice) !== normalize(invoiceInput.value)) {
            const kilometerSelect = form.querySelector('select[name="kilometer_id"]');
            const kilometerInput = form.querySelector('[data-group-kilometer-input]');
            if (kilometerSelect) kilometerSelect.value = '';
            if (kilometerInput) kilometerInput.value = '';
            form.dataset.selectedInvoice = '';
        }

        const terms = normalize(invoiceInput.value).split(/\s+/).filter(Boolean);
        const options = optionsList.querySelectorAll('.group-invoice-option');
        const emptyMessage = optionsList.querySelector('[data-group-invoice-empty]');
        let visibleCount = 0;
        let exactMatch = null;

        options.forEach(function(option) {
            const searchText = normalize(option.dataset.search);
            if (searchText === normalize(invoiceInput.value)) exactMatch = exactMatch || option;
            const matches = terms.length > 0 && terms.every(function(term) {
                return searchText.includes(term);
            });
            option.hidden = !matches || visibleCount >= 8;
            if (matches) visibleCount++;
        });

        if (emptyMessage) emptyMessage.hidden = terms.length === 0 || visibleCount > 0;
        optionsList.hidden = terms.length === 0;
        invoiceInput.setAttribute('aria-expanded', String(terms.length > 0));

        if (exactMatch) selectInvoiceOption(form, exactMatch);
    });

    document.addEventListener('mousedown', function(event) {
        if (event.target instanceof Element && event.target.closest('.group-invoice-option')) {
            event.preventDefault();
        }
    });

    document.addEventListener('click', function(event) {
        const option = event.target instanceof Element
            ? event.target.closest('.group-invoice-option')
            : null;

        if (option) {
            const form = option.closest('.group-form');
            selectInvoiceOption(form, option);
            option.closest('[data-group-invoice-options]').hidden = true;
            return;
        }

        if (event.target instanceof Element && !event.target.closest('[data-group-invoice-combobox]')) {
            document.querySelectorAll('[data-group-invoice-options]').forEach(function(list) {
                list.hidden = true;
                const input = list.parentElement.querySelector('[data-group-invoice-input]');
                if (input) input.setAttribute('aria-expanded', 'false');
            });
        }
    });

    document.addEventListener('keydown', function(event) {
        if (event.key !== 'Escape' || !(event.target instanceof Element)) return;
        const input = event.target.closest('[data-group-invoice-input]');
        if (!input) return;
        input.parentElement.querySelector('[data-group-invoice-options]').hidden = true;
        input.setAttribute('aria-expanded', 'false');
    });

    const createModal = document.getElementById('createGroupModal');
    if (createModal) {
        const hideSuggestions = function() {
            const form = createModal.querySelector('.group-form');
            if (!form) return;
            form.querySelectorAll('[data-group-invoice-options]').forEach(function(list) {
                list.hidden = true;
            });
            form.querySelectorAll('[data-group-invoice-input]').forEach(function(input) {
                input.setAttribute('aria-expanded', 'false');
            });
        };
        createModal.addEventListener('show.bs.modal', hideSuggestions);
        createModal.addEventListener('hidden.bs.modal', hideSuggestions);
    }
})();
</script>
@endsection
