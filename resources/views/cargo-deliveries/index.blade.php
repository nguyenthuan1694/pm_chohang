@extends('layouts.dashboard')

@section('title', 'DS Thông tin chở hàng | ' . config('app.name', 'Laravel'))

@section('content')
<div class="dashboard-overview cargo-page">
    @if (session('status'))
        <div class="alert alert-success dashboard-alert" role="alert">{{ session('status') }}</div>
    @endif

    <div class="page-heading-row">
        <div><p class="dashboard-breadcrumb">Workspace / Cargo</p><h1 class="page-heading">Thông tin chở hàng</h1><p class="page-subtitle">Theo dõi phiếu chở hàng, khối lượng và trạng thái giao nhận.</p></div>
        <button type="button" class="btn kilometer-add-button" data-bs-toggle="modal" data-bs-target="#createCargoModal"><span aria-hidden="true">+</span> Thêm thông tin giao hàng</button>
    </div>

    <section class="kilometer-toolbar">
        <form method="GET" action="{{ route('cargo-deliveries.index') }}" class="kilometer-search-form">
            <label class="visually-hidden" for="cargo-date">Tìm theo ngày</label>
            <input id="cargo-date" name="date" type="date" value="{{ $date }}" class="cargo-date-filter-input" title="Tìm theo ngày" aria-label="Tìm theo ngày">
            <label class="visually-hidden" for="cargo-search">Tìm kiếm</label>
            <span class="search-icon" aria-hidden="true">⌕</span>
            <input id="cargo-search" name="search" type="search" value="{{ $search }}" placeholder="Tìm tên phiếu, nhân viên, nhóm, địa chỉ...">
            @if ($search !== '' || $date !== '' || $fromDate !== '' || $toDate !== '')
                <a class="clear-search" href="{{ route('cargo-deliveries.index') }}" title="Xóa tìm kiếm" aria-label="Xóa tìm kiếm">×</a>
            @endif
            <button type="submit" class="kilometer-search-button">Tìm kiếm</button>
        </form>
        <span class="kilometer-updated">Cập nhật {{ now()->format('d/m/Y') }}</span>
    </section>

    <section class="kilometer-table-card">
        <div class="kilometer-table-wrap">
            <table class="kilometer-table cargo-table">
                <thead><tr><th>NGÀY</th><th>DUYỆT ĐƠN</th><th>TÊN XUẤT PHIẾU</th><th>NHÓM</th><th>ĐỊA CHỈ</th><th>SỐ CHUYẾN</th><th>SỐ BAO</th><th>SỐ KG</th><th>SỐ KM</th><th>CHÀNH</th><th>XE ÔM</th><th>THÔNG TIN GHI BAO</th><th>GHI CHÚ</th><th>TÁC VỤ</th></tr></thead>
                <tbody id="cargo-deliveries-tbody">
                    @forelse ($deliveries as $delivery)
                        @include('cargo-deliveries._row', ['delivery' => $delivery])
                    @empty
                        <tr class="empty-table-row"><td colspan="14" class="empty-table-cell"><strong>Chưa có thông tin chở hàng</strong><span>{{ ($search !== '' || $date !== '' || $fromDate !== '' || $toDate !== '') ? 'Không tìm thấy dữ liệu phù hợp.' : 'Dữ liệu sẽ hiển thị sau khi được thêm.' }}</span></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($deliveries->hasPages())<div class="kilometer-pagination">{{ $deliveries->links() }}</div>@endif
    </section>
</div>

<div class="modal fade" id="createCargoModal" tabindex="-1" aria-labelledby="createCargoModalLabel" aria-hidden="true"><div class="modal-dialog modal-xl modal-dialog-centered"><div class="modal-content kilometer-modal-content"><div class="modal-header"><div><span class="panel-kicker">New cargo record</span><h2 class="modal-title" id="createCargoModalLabel">Thêm thông tin giao hàng</h2></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button></div><div class="modal-body">@include('cargo-deliveries._form', ['delivery' => null, 'isEditing' => false])</div></div></div></div>

@foreach ($deliveries as $delivery)
    @include('cargo-deliveries._edit_modal', ['delivery' => $delivery, 'employees' => $employees, 'groups' => $groups])
@endforeach
<div id="dynamic-edit-modals"></div>

<style>
.cargo-kilometer-table-wrap {
    max-height: 290px !important;
    overflow: auto !important;
    background: #f7faf7;
    border: 1px solid var(--line);
    border-radius: 8px;
    position: relative;
}
.cargo-kilometer-scroll-pane {
    min-width: 980px;
    width: 100%;
}
.cargo-kilometer-header {
    display: grid !important;
    grid-template-columns: 24px 95px 125px 75px minmax(150px, 1.3fr) 60px 60px 70px 90px minmax(120px, 1fr) !important;
    align-items: center;
    gap: .55rem;
    padding: .65rem 1.15rem !important;
    background: #e2ede4;
    border-bottom: 1px solid var(--line);
    font-size: .72rem;
    font-weight: 800;
    color: #244234;
    text-transform: uppercase;
    letter-spacing: .03em;
    position: sticky !important;
    top: 0 !important;
    z-index: 5 !important;
}
.cargo-kilometer-list {
    display: grid !important;
    gap: .45rem !important;
    padding: .5rem !important;
    background: transparent !important;
    border: 0 !important;
    max-height: none !important;
    overflow: visible !important;
}
.cargo-kilometer-option {
    display: grid !important;
    grid-template-columns: 24px 95px 125px 75px minmax(150px, 1.3fr) 60px 60px 70px 90px minmax(120px, 1fr) !important;
    align-items: center;
    gap: .55rem;
    padding: .65rem .65rem !important;
    background: #fff;
    border: 1px solid #e2eae3;
    border-radius: 5px;
    font-size: .72rem;
    cursor: pointer;
    min-width: 0 !important;
    width: 100% !important;
    box-sizing: border-box;
}
.cargo-kilometer-option:hover { border-color: var(--mint); background: #fbfdfb; }
.cargo-kilometer-option:has(input:checked) { background: #eaf5ec; border-color: var(--mint-deep); }
.cargo-kilometer-option[hidden],
.cargo-kilometer-option.is-hidden {
    display: none !important;
}
.cargo-option-invoice { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: var(--ink); }
.cargo-option-group { white-space: nowrap; }
.cargo-option-address { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.cargo-option-note { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
@keyframes rowHighlight {
    0% { background-color: #d1fae5 !important; }
    100% { background-color: transparent; }
}
.row-newly-added {
    animation: rowHighlight 3s ease-out;
}
.cargo-form-alert {
    padding: .75rem 1rem;
    border-radius: 6px;
    font-size: .82rem;
    font-weight: 700;
}
.cargo-page .kilometer-search-form {
    max-width: 760px;
}
.cargo-date-filter-input {
    flex: 0 0 auto !important;
    width: 145px !important;
    min-width: 130px !important;
    padding: .48rem .65rem !important;
    font-size: .82rem !important;
    border: 1px solid var(--line) !important;
    border-radius: 6px !important;
    background: #f8faf8 !important;
    color: var(--ink) !important;
    outline: 0 !important;
    margin-right: .35rem !important;
    cursor: pointer;
    transition: all .15s ease !important;
}
.cargo-date-filter-input:focus {
    border-color: var(--mint-deep) !important;
    background: #fff !important;
    box-shadow: 0 0 0 .15rem rgba(36, 107, 75, .12) !important;
}
@media (max-width: 767.98px) {
    .cargo-page .kilometer-search-form {
        flex-wrap: wrap;
        gap: .4rem;
    }
    .cargo-date-filter-input {
        width: 100% !important;
        margin-right: 0 !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function normalizeSearchText(str) {
        if (!str) return '';
        return str
            .toString()
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/đ/g, 'd')
            .replace(/Đ/g, 'd')
            .replace(/[^\w\s]/gi, ' ')
            .trim();
    }

    document.addEventListener('input', function(e) {
        const input = e.target;
        if (!input || !input.matches('[data-cargo-kilometer-search]')) return;

        const form = input.closest('.cargo-delivery-form');
        if (!form) return;

        const rawTerm = input.value.trim();
        const options = form.querySelectorAll('.cargo-kilometer-option');
        const emptyMsg = form.querySelector('.cargo-kilometer-empty');

        if (!rawTerm) {
            options.forEach(function(opt) {
                opt.hidden = false;
                opt.classList.remove('is-hidden');
                opt.style.removeProperty('display');
            });
            if (emptyMsg) emptyMsg.style.display = 'none';
            return;
        }

        const normQuery = normalizeSearchText(rawTerm);
        const terms = normQuery.split(/\s+/).filter(Boolean);
        let visibleCount = 0;

        options.forEach(function(opt) {
            const searchKey = normalizeSearchText(opt.dataset.search || '');
            const matched = terms.every(function(t) {
                return searchKey.includes(t);
            });

            opt.hidden = !matched;
            if (matched) {
                opt.classList.remove('is-hidden');
                opt.style.removeProperty('display');
                visibleCount++;
            } else {
                opt.classList.add('is-hidden');
                opt.style.setProperty('display', 'none', 'important');
            }
        });

        if (emptyMsg) {
            emptyMsg.style.display = visibleCount === 0 ? 'block' : 'none';
        }
    });

    document.addEventListener('change', async function(e) {
        const select = e.target;
        if (!select || !select.matches('[data-cargo-status-form] select[name="delivery_status"]')) return;

        const form = select.form;
        const feedback = form.querySelector('[data-cargo-status-feedback]');
        const statusClasses = ['status-pending', 'status-delivered', 'status-failed', 'status-delivering'];
        select.classList.remove(...statusClasses);
        select.classList.add(`status-${select.value}`);
        const formData = new FormData(form);
        select.disabled = true;
        if (feedback) {
            feedback.hidden = true;
            feedback.textContent = '';
        }

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });
            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(result.message || 'Không thể cập nhật trạng thái đơn.');
            }
            select.value = result.delivery_status;
            select.classList.remove(...statusClasses);
            select.classList.add(`status-${result.delivery_status}`);
            if (feedback) {
                feedback.textContent = result.message;
                feedback.hidden = false;
            }
        } catch (error) {
            console.error('Không thể cập nhật trạng thái đơn.', error);
        } finally {
            select.disabled = false;
        }
    });

    const createModal = document.getElementById('createCargoModal');
    if (createModal) {
        const form = createModal.querySelector('form.cargo-delivery-form');
        const alertDiv = form ? form.querySelector('.cargo-form-alert') : null;
        const submitBtn = form ? form.querySelector('button[type="submit"]') : null;

        const resetSearch = function() {
            if (!form) return;
            form.querySelectorAll('.cargo-kilometer-option').forEach(function(opt) {
                opt.hidden = false;
                opt.classList.remove('is-hidden');
                opt.style.removeProperty('display');
            });
            form.querySelectorAll('[data-cargo-kilometer-search]').forEach(function(inp) {
                inp.value = '';
            });
            const emptyMsg = form.querySelector('.cargo-kilometer-empty');
            if (emptyMsg) emptyMsg.style.display = 'none';
            if (alertDiv) {
                alertDiv.style.display = 'none';
                alertDiv.innerHTML = '';
            }
        };
        createModal.addEventListener('show.bs.modal', resetSearch);
        createModal.addEventListener('hidden.bs.modal', resetSearch);

        if (form && !form.dataset.ajaxBound) {
            form.dataset.ajaxBound = 'true';
            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                const selectedRadio = form.querySelector('input[name="group_id"]:checked');
                if (!selectedRadio) {
                    if (alertDiv) {
                        alertDiv.className = 'cargo-form-alert alert alert-danger';
                        alertDiv.innerHTML = '⚠️ Vui lòng chọn một thông tin trong danh sách giao hàng bên dưới.';
                        alertDiv.style.display = 'block';
                    }
                    const listWrap = form.querySelector('.cargo-kilometer-table-wrap');
                    if (listWrap) {
                        listWrap.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }
                    return;
                }

                if (!form.reportValidity()) {
                    return;
                }

                const originalBtnText = submitBtn ? submitBtn.textContent : 'Lưu thông tin';
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Đang lưu...';
                }

                if (alertDiv) {
                    alertDiv.style.display = 'none';
                    alertDiv.innerHTML = '';
                }

                try {
                    const formData = new FormData(form);
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        }
                    });

                    const result = await response.json();

                    if (response.ok && result.success) {
                        // 1. Show success alert inside modal
                        if (alertDiv) {
                            alertDiv.className = 'cargo-form-alert alert alert-success';
                            alertDiv.innerHTML = '✓ ' + (result.message || 'Đã lưu thông tin chở hàng thành công!') + ' Dữ liệu đã được thêm ngoài danh sách.';
                            alertDiv.style.display = 'block';
                        }

                        // 2. Prepend row to table outside modal
                        const tbody = document.getElementById('cargo-deliveries-tbody') || document.querySelector('.cargo-table tbody');
                        if (tbody && result.row_html) {
                            const emptyRow = tbody.querySelector('.empty-table-row, .empty-table-cell');
                            if (emptyRow) {
                                const tr = emptyRow.closest('tr');
                                if (tr) tr.remove();
                            }
                            const rowTemplate = document.createElement('template');
                            rowTemplate.innerHTML = result.row_html.trim();
                            const newRow = rowTemplate.content.firstElementChild;
                            const newTripCount = Number(newRow?.dataset.tripCount);
                            const rowToInsertBefore = Array.from(tbody.querySelectorAll('tr[data-trip-count]'))
                                .find((row) => Number(row.dataset.tripCount) > newTripCount);

                            if (newRow) {
                                tbody.insertBefore(newRow, rowToInsertBefore || null);
                            }

                            if (newRow) {
                                newRow.classList.add('row-newly-added');
                            }
                        }

                        // 3. Append dynamic edit modal
                        if (result.modal_html) {
                            const dynamicModalsContainer = document.getElementById('dynamic-edit-modals') || document.body;
                            dynamicModalsContainer.insertAdjacentHTML('beforeend', result.modal_html);
                        }

                        // 4. Giữ lại data toàn bộ các field theo yêu cầu:
                        // Tên chở hàng, Chọn nhân viên, Số chuyến, Số bao, Số kg, Ghi chú, Thông tin danh sách giao hàng
                        if (alertDiv) {
                            alertDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                        }

                    } else {
                        let errorMsg = result.message || 'Có lỗi xảy ra khi lưu thông tin. Vui lòng kiểm tra lại.';
                        if (result.errors) {
                            const firstKey = Object.keys(result.errors)[0];
                            if (firstKey && result.errors[firstKey][0]) {
                                errorMsg = result.errors[firstKey][0];
                            }
                        }
                        if (alertDiv) {
                            alertDiv.className = 'cargo-form-alert alert alert-danger';
                            alertDiv.innerHTML = '✕ ' + errorMsg;
                            alertDiv.style.display = 'block';
                        }
                    }
                } catch (err) {
                    console.error('Lỗi khi gửi form:', err);
                    if (alertDiv) {
                        alertDiv.className = 'cargo-form-alert alert alert-danger';
                        alertDiv.innerHTML = '✕ Không thể kết nối đến máy chủ. Vui lòng thử lại.';
                        alertDiv.style.display = 'block';
                    }
                } finally {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.textContent = originalBtnText;
                    }
                }
            });
        }
    }
});
</script>
@endsection
