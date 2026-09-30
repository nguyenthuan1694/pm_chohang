<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'thumbnail',
        'name',
        'started_at',
        'address',
        'is_support',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'date',
            'is_support' => 'boolean',
        ];
    }

    public function getThumbnailUrlAttribute(): string
    {
        return asset('images/default-avatar.svg');
    }
}
