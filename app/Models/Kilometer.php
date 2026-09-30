<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kilometer extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_user_id',
        'group_id',
        'invoice_name',
        'date',
        'address',
        'ward',
        'district',
        'distance_km',
        'carrier_fee',
        'motorbike_driver',
        'package_note',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'distance_km' => 'decimal:2',
            'carrier_fee' => 'decimal:2',
        ];
    }

    // Accessors/Mutators for backwards compatibility
    public function getNgayAttribute() { return $this->date; }
    public function setNgayAttribute($value) { $this->attributes['date'] = $value; }

    public function getDiaChiAttribute() { return $this->address; }
    public function setDiaChiAttribute($value) { $this->attributes['address'] = $value; }

    public function getPhuongAttribute() { return $this->ward; }
    public function setPhuongAttribute($value) { $this->attributes['ward'] = $value; }

    public function getQuanAttribute() { return $this->district; }
    public function setQuanAttribute($value) { $this->attributes['district'] = $value; }

    public function getKmAttribute() { return $this->distance_km; }
    public function setKmAttribute($value) { $this->attributes['distance_km'] = $value; }

    public function getTienChanhAttribute() { return $this->carrier_fee; }
    public function setTienChanhAttribute($value) { $this->attributes['carrier_fee'] = $value; }

    public function getXeOmAttribute() { return $this->motorbike_driver; }
    public function setXeOmAttribute($value) { $this->attributes['motorbike_driver'] = $value; }

    public function getThongTinGhiBaoAttribute() { return $this->package_note; }
    public function setThongTinGhiBaoAttribute($value) { $this->attributes['package_note'] = $value; }
}
