<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    public function latestCargoDelivery(): HasOne
    {
        return $this->hasOne(CargoDelivery::class)->latestOfMany();
    }

    public function canViewDeliveryStatus(?User $user = null): bool
    {
        $user = $user ?: auth()->user();
        if (!$user) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->role === 'group') {
            return ($user->group_id && (int) $this->group_id === (int) $user->group_id)
                || ($user->group_id && in_array(trim((string) $this->group_name), ['Nhóm ' . $user->group_id, (string) $user->group_id]))
                || ($user->name && (string) $this->group_name === (string) $user->name)
                || ($this->owner_user_id && (int) $this->owner_user_id === (int) $user->id)
                || ((int) $this->id === (int) $user->group_id);
        }

        return false;
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status === 'active' ? 'Active' : 'Chưa Active';
    }

    public static function removeVietnameseAccents(?string $str): string
    {
        if (! $str) return '';
        static $map = [
            'à'=>'a','á'=>'a','ạ'=>'a','ả'=>'a','ã'=>'a','â'=>'a','ầ'=>'a','ấ'=>'a','ậ'=>'a','ẩ'=>'a','ẫ'=>'a','ă'=>'a','ằ'=>'a','ắ'=>'a','ặ'=>'a','ẳ'=>'a','ẵ'=>'a',
            'è'=>'e','é'=>'e','ẹ'=>'e','ẻ'=>'e','ẽ'=>'e','ê'=>'e','ề'=>'e','ế'=>'e','ệ'=>'e','ể'=>'e','ễ'=>'e',
            'ì'=>'i','í'=>'i','ị'=>'i','ỉ'=>'i','ĩ'=>'i',
            'ò'=>'o','ó'=>'o','ọ'=>'o','ỏ'=>'o','õ'=>'o','ô'=>'o','ồ'=>'o','ố'=>'o','ộ'=>'o','ổ'=>'o','ỗ'=>'o','ơ'=>'o','ờ'=>'o','ớ'=>'o','ợ'=>'o','ở'=>'o','ỡ'=>'o',
            'ù'=>'u','ú'=>'u','ụ'=>'u','ủ'=>'u','ũ'=>'u','ư'=>'u','ừ'=>'u','ứ'=>'u','ự'=>'u','ử'=>'u','ữ'=>'u',
            'ỳ'=>'y','ý'=>'y','ỵ'=>'y','ỷ'=>'y','ỹ'=>'y',
            'đ'=>'d','Đ'=>'d',
            'À'=>'a','Á'=>'a','Ạ'=>'a','Ả'=>'a','Ã'=>'a','Â'=>'a','Ầ'=>'a','Ấ'=>'a','Ậ'=>'a','Ẩ'=>'a','Ẫ'=>'a','Ă'=>'a','Ằ'=>'a','Ắ'=>'a','Ặ'=>'a','Ẳ'=>'a','Ẵ'=>'a',
            'È'=>'e','É'=>'e','Ẹ'=>'e','Ẻ'=>'e','Ẽ'=>'e','Ê'=>'e','Ề'=>'e','Ế'=>'e','Ệ'=>'e','Ể'=>'e','Ễ'=>'e',
            'Ì'=>'i','Í'=>'i','Ị'=>'i','Ỉ'=>'i','Ĩ'=>'i',
            'Ò'=>'o','Ó'=>'o','Ọ'=>'o','Ỏ'=>'o','Õ'=>'o','Ô'=>'o','Ồ'=>'o','Ố'=>'o','Ộ'=>'o','Ổ'=>'o','Ỗ'=>'o','Ơ'=>'o','Ờ'=>'o','Ớ'=>'o','Ợ'=>'o','Ở'=>'o','Ỡ'=>'o',
            'Ù'=>'u','Ú'=>'u','Ụ'=>'u','Ủ'=>'u','Ũ'=>'u','Ư'=>'u','Ừ'=>'u','Ứ'=>'u','Ự'=>'u','Ử'=>'u','Ữ'=>'u',
            'Ỳ'=>'y','Ý'=>'y','Ỵ'=>'y','Ỷ'=>'y','Ỹ'=>'y',
        ];
        return strtr($str, $map);
    }

    public function getSearchKeyAttribute(): string
    {
        if (isset($this->attributes['search_key'])) {
            return $this->attributes['search_key'];
        }

        $searchParts = array_filter([
            $this->invoice_name,
            $this->group_name,
            $this->address,
            $this->ward,
            $this->district,
            $this->package_note,
        ]);
        if (!empty($this->district)) {
            $searchParts[] = 'quận ' . $this->district;
            $searchParts[] = 'quan ' . $this->district;
        }
        if (!empty($this->ward)) {
            $searchParts[] = 'phường ' . $this->ward;
            $searchParts[] = 'phuong ' . $this->ward;
        }
        $combined = implode(' ', $searchParts);
        $lower = mb_strtolower($combined, 'UTF-8');
        $unaccented = self::removeVietnameseAccents($lower);
        $noSpaces = str_replace(' ', '', $unaccented);

        return $lower . ' ' . $unaccented . ' ' . $noSpaces;
    }
}
