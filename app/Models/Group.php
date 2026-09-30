<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'owner_user_id',
        'kilometer_id',
        'group_date',
        'invoice_name',
        'group_name',
        'address',
        'ward',
        'district',
        'distance_km',
        'carrier_fee',
        'motorbike_driver',
        'package_note',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'group_date' => 'datetime',
            'distance_km' => 'decimal:2',
            'carrier_fee' => 'decimal:2',
        ];
    }

    public function kilometer(): BelongsTo
    {
        return $this->belongsTo(Kilometer::class);
    }

    public function cargoDeliveries(): HasMany
    {
        return $this->hasMany(CargoDelivery::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status === 'active' ? 'Active' : 'Chưa Active';
    }
}
