<tr id="delivery-row-{{ $delivery->id }}">
    <td class="date-cell">{{ $delivery->delivery_date->format('d/m/Y H:i') }}</td>
    <td>
        <form method="POST" action="{{ route('cargo-deliveries.status', $delivery) }}">
            @csrf
            @method('PATCH')
            <select name="delivery_status" class="delivery-status-select status-{{ $delivery->delivery_status }}" onchange="this.form.submit()">
                <option value="pending" @selected($delivery->delivery_status === 'pending')>Chưa giao</option>
                <option value="delivered" @selected($delivery->delivery_status === 'delivered')>Đã giao</option>
                <option value="failed" @selected($delivery->delivery_status === 'failed')>Đang giao</option>
            </select>
        </form>
    </td>
    <td><strong>{{ $delivery->invoice_name }}</strong><small class="table-subtext">{{ $delivery->employee?->name }}</small></td>
    <td><span class="group-badge">{{ $delivery->group_name }}</span></td>
    <td class="address-cell">{{ $delivery->address }}<small class="table-subtext">{{ $delivery->ward }}, {{ $delivery->district }}</small></td>
    <td class="number-cell">{{ $delivery->trip_count }}</td>
    <td class="number-cell">{{ $delivery->package_count }}</td>
    <td class="number-cell">{{ number_format((float) $delivery->weight_kg, 2, ',', '.') }}</td>
    <td class="number-cell">{{ number_format((float) $delivery->distance_km, 2, ',', '.') }}</td>
    <td class="money-cell">{{ number_format((float) $delivery->carrier_fee, 0, ',', '.') }} đ</td>
    <td>{{ $delivery->motorbike_driver ?: '—' }}</td>
    <td class="note-cell">{{ $delivery->package_note ?: '—' }}</td>
    <td class="note-cell">{{ $delivery->note ?: '—' }}</td>
    <td>
        <div class="kilometer-actions">
            <button type="button" class="kilometer-action-button edit" data-bs-toggle="modal" data-bs-target="#editCargoModal-{{ $delivery->id }}">Sửa</button>
            <form method="POST" action="{{ route('cargo-deliveries.destroy', $delivery) }}" onsubmit="return confirm('Bạn có chắc muốn xóa thông tin này?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="kilometer-action-button delete">Xóa</button>
            </form>
        </div>
    </td>
</tr>
