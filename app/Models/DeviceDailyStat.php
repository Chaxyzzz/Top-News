<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceDailyStat extends Model
{
    use HasFactory;

    protected $table = 'device_daily_stats';

    protected $fillable = [
        'date',
        'device_type',
        'views',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'views' => 'integer',
        ];
    }
}
