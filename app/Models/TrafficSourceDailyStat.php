<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrafficSourceDailyStat extends Model
{
    use HasFactory;

    protected $table = 'traffic_source_daily_stats';

    protected $fillable = [
        'date',
        'source_type',
        'source_domain',
        'views',
        'unique_views',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'views' => 'integer',
            'unique_views' => 'integer',
        ];
    }
}
