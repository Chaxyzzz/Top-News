<?php

namespace App\Models;

use App\Enums\ReactionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleReaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'article_id',
        'user_id',
        'reaction_type',
    ];

    protected $casts = [
        'reaction_type' => ReactionType::class,
    ];

    /**
     * Reacted article.
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * User who reacted.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
