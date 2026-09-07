<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteDailyStat extends Model
{
    use HasFactory;

    protected $table = 'site_daily_stats';

    protected $fillable = [
        'date',
        'page_views',
        'unique_sessions',
        'article_views',
        'searches',
        'comments_submitted',
        'newsletter_subscriptions',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'page_views' => 'integer',
            'unique_sessions' => 'integer',
            'article_views' => 'integer',
            'searches' => 'integer',
            'comments_submitted' => 'integer',
            'newsletter_subscriptions' => 'integer',
        ];
    }
}
