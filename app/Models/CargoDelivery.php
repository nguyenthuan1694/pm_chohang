<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CargoDelivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'employee_id',
        'kilometer_id',
        'delivery_date',
        'invoice_name',
        'group_name',
        'address',
        'ward',
        'district',
        'trip_count',
        'package_count',
        'weight_kg',
        'distance_km',
        'carrier_fee',
        'motorbike_driver',
        'package_note',
        'note',
        'delivery_status',
    ];

    protected function casts(): array
    {
        return [
            'delivery_date' => 'datetime',
            'weight_kg' => 'decimal:2',
            'distance_km' => 'decimal:2',
            'carrier_fee' => 'decimal:2',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function kilometer(): BelongsTo
    {
        return $this->belongsTo(Kilometer::class);
    }

    public function getDeliveryStatusLabelAttribute(): string
    {
        return match ($this->delivery_status) {
            'delivered' => 'Đã giao',
            'failed', 'delivering' => 'Đang giao',
            default => 'Chưa giao',
        };
    }
}
