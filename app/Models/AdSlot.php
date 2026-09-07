<?php

namespace App\Models;

use App\Enums\AdDeviceScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'name',
        'description',
        'placement',
        'device_scope',
        'width',
        'height',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'device_scope' => AdDeviceScope::class,
            'width' => 'integer',
            'height' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function advertisements(): HasMany
    {
        return $this->hasMany(Advertisement::class);
    }
}
