<div class="modal fade" id="editCargoModal-{{ $delivery->id }}" tabindex="-1" aria-labelledby="editCargoModalLabel-{{ $delivery->id }}" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content kilometer-modal-content">
            <div class="modal-header">
                <div>
                    <span class="panel-kicker">Edit cargo record</span>
                    <h2 class="modal-title" id="editCargoModalLabel-{{ $delivery->id }}">Sửa thông tin chở hàng</h2>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                @include('cargo-deliveries._form', [
                    'delivery' => $delivery,
                    'formAction' => route('cargo-deliveries.update', $delivery),
                    'formMethod' => 'PUT',
                    'employees' => $employees,
                    'groups' => $groups,
                ])
            </div>
        </div>
    </div>
</div>
