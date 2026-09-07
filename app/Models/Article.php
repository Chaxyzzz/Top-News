<?php

namespace App\Models;

use App\Enums\ArticleStatus;
use App\Enums\ArticleType;
use App\Enums\ReactionType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'title',
        'slug',
        'subtitle',
        'excerpt',
        'content',
        'content_type',
        'status',
        'category_id',
        'author_id',
        'editor_id',
        'featured_media_id',
        'featured_image',
        'featured_image_caption',
        'featured_image_alt',
        'is_featured',
        'is_breaking',
        'homepage_priority',
        'is_editor_choice',
        'is_sponsored',
        'sponsor_name',
        'sponsor_url',
        'allow_comments',
        'correction_note',
        'corrected_at',
        'source_name',
        'source_url',
        'seo_title',
        'seo_description',
        'canonical_url',
        'robots_index',
        'robots_follow',
        'reading_time',
        'views_count',
        'submitted_at',
        'approved_at',
        'published_at',
        'scheduled_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'content_type' => ArticleType::class,
            'status' => ArticleStatus::class,
            'is_featured' => 'boolean',
            'is_breaking' => 'boolean',
            'homepage_priority' => 'integer',
            'is_editor_choice' => 'boolean',
            'is_sponsored' => 'boolean',
            'allow_comments' => 'boolean',
            'robots_index' => 'boolean',
            'robots_follow' => 'boolean',
            'reading_time' => 'integer',
            'views_count' => 'integer',
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
            'published_at' => 'datetime',
            'scheduled_at' => 'datetime',
            'corrected_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Boot model events for automatic UUID, Slug, and Reading Time calculation.
     */
    protected static function booted(): void
    {
        static::creating(function (Article $article) {
            if (empty($article->uuid)) {
                $article->uuid = (string) Str::uuid();
            }

            if (empty($article->slug)) {
                $article->slug = static::generateUniqueSlug($article->title);
            }

            if (empty($article->reading_time)) {
                $article->reading_time = static::calculateReadingTime($article->content);
            }
        });

        static::updating(function (Article $article) {
            if ($article->isDirty('content')) {
                $article->reading_time = static::calculateReadingTime($article->content);
            }
        });
    }

    /**
     * Get the full URL of the featured image (supports Media model, legacy path, or remote URL).
     */
    protected function featuredImageUrl(): Attribute
    {
        return Attribute::make(get: function () {
            // 1. Prefer managed Media relation
            if ($this->featuredMedia) {
                return $this->featuredMedia->large_url;
            }

            // 2. Video thumbnail fallback
            if ($this->video && $this->video->thumbnail_url) {
                return $this->video->thumbnail_url;
            }

            // 3. Photo Story cover fallback
            if ($this->photoStory && $this->photoStory->gallery && $this->photoStory->gallery->cover_image_url) {
                return $this->photoStory->gallery->cover_image_url;
            }

            // 4. Legacy path fallback
            if (empty($this->featured_image)) {
                return null;
            }

            if (str_starts_with($this->featured_image, 'http://') || str_starts_with($this->featured_image, 'https://')) {
                return $this->featured_image;
            }

            return Storage::url($this->featured_image);
        });
    }

    /**
     * Calculate reading time (~220 words per minute).
     */
    public static function calculateReadingTime(?string $content): int
    {
        if (empty($content)) {
            return 1;
        }

        $wordCount = str_word_count(strip_tags($content));

        return (int) max(1, ceil($wordCount / 220));
    }

    /**
     * Generate unique slug for an article.
     */
    public static function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        while (static::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        return $slug;
    }

    // ==========================================
    // CENTRALIZED SCOPES (RULE 4 & RULE 237)
    // ==========================================

    /**
     * Determine whether the article is currently published publicly.
     */
    public function isPublished(): bool
    {
        return $this->status === ArticleStatus::Published
            && $this->published_at !== null
            && $this->published_at->isPast()
            && $this->deleted_at === null;
    }

    /**
     * Scope for publicly visible published articles.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', ArticleStatus::Published->value)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->whereNull('deleted_at');
    }

    /**
     * Scope for scheduled articles.
     */
    public function scopeScheduled(Builder $query): Builder
    {
        return $query->where('status', ArticleStatus::Scheduled->value);
    }

    /**
     * Scope for draft articles.
     */
    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', ArticleStatus::Draft->value);
    }

    /**
     * Scope for articles waiting for review.
     */
    public function scopeNeedsReview(Builder $query): Builder
    {
        return $query->where('status', ArticleStatus::Submitted->value);
    }

    /**
     * Scope for articles currently in review.
     */
    public function scopeInReview(Builder $query): Builder
    {
        return $query->where('status', ArticleStatus::InReview->value);
    }

    /**
     * Scope for articles needing revision.
     */
    public function scopeRevisionRequested(Builder $query): Builder
    {
        return $query->where('status', ArticleStatus::RevisionRequested->value);
    }

    /**
     * Scope for approved articles ready to publish.
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', ArticleStatus::Approved->value);
    }

    /**
     * Scope for featured articles.
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for breaking news articles.
     */
    public function scopeBreaking(Builder $query): Builder
    {
        return $query->where('is_breaking', true);
    }

    /**
     * Scope for editor's choice articles.
     */
    public function scopeEditorChoice(Builder $query): Builder
    {
        return $query->where('is_editor_choice', true);
    }

    /**
     * Scope for opinion column articles.
     */
    public function scopeOpinion(Builder $query): Builder
    {
        return $query->where('content_type', ArticleType::Opinion->value);
    }

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    /**
     * Get the primary category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the journalist author.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Get the reviewing/approving editor.
     */
    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'editor_id');
    }

    /**
     * Get associated tags.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'article_tag');
    }

    /**
     * Get snapshot revisions.
     */
    public function revisions(): HasMany
    {
        return $this->hasMany(ArticleRevision::class)->orderBy('revision_number', 'desc');
    }

    /**
     * Get editorial review action history.
     */
    public function editorialActions(): HasMany
    {
        return $this->hasMany(ArticleEditorialAction::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get daily reading statistics.
     */
    public function dailyStats(): HasMany
    {
        return $this->hasMany(ArticleDailyStat::class)->orderBy('date', 'desc');
    }

    /**
     * Get the featured media record.
     */
    public function featuredMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'featured_media_id');
    }

    /**
     * Get the video metadata for video articles.
     */
    public function video(): HasOne
    {
        return $this->hasOne(ArticleVideo::class);
    }

    /**
     * Get the photo story gallery relationship.
     */
    public function photoStory(): HasOne
    {
        return $this->hasOne(PhotoStory::class);
    }

    /**
     * Bookmarks for this article.
     *
     * @return HasMany<Bookmark>
     */
    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    /**
     * Comments on this article.
     *
     * @return HasMany<Comment>
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Publicly approved top-level comments with approved replies.
     *
     * @return HasMany<Comment>
     */
    public function approvedComments(): HasMany
    {
        return $this->comments()
            ->approved()
            ->topLevel()
            ->with(['user', 'approvedReplies.user'])
            ->oldest();
    }

    /**
     * Reactions to this article.
     *
     * @return HasMany<ArticleReaction>
     */
    public function reactions(): HasMany
    {
        return $this->hasMany(ArticleReaction::class);
    }

    /**
     * Check if article is bookmarked by user.
     */
    public function isBookmarkedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $this->bookmarks()->where('user_id', $user->id)->exists();
    }

    /**
     * Get user's current reaction type to this article.
     */
    public function getUserReaction(?User $user): ?ReactionType
    {
        if (! $user) {
            return null;
        }

        return $this->reactions()->where('user_id', $user->id)->first()?->reaction_type;
    }

    /**
     * Get aggregate reaction counts grouped by reaction type.
     *
     * @return array<string, int>
     */
    public function getReactionCounts(): array
    {
        $counts = [
            'useful' => 0,
            'interesting' => 0,
            'important' => 0,
        ];

        $results = $this->reactions()
            ->selectRaw('reaction_type, count(*) as count')
            ->groupBy('reaction_type')
            ->pluck('count', 'reaction_type')
            ->all();

        foreach ($results as $type => $count) {
            $key = $type instanceof ReactionType ? $type->value : (string) $type;
            $counts[$key] = (int) $count;
        }

        return $counts;
    }

    /**
     * Breaking news entries linked to this article.
     *
     * @return HasMany<BreakingNews>
     */
    public function breakingNews(): HasMany
    {
        return $this->hasMany(BreakingNews::class);
    }

    /**
     * Homepage sections in which this article is manually curated.
     *
     * @return BelongsToMany<HomepageSection>
     */
    public function curatedInSections(): BelongsToMany
    {
        return $this->belongsToMany(HomepageSection::class, 'homepage_section_articles')
            ->withPivot('sort_order');
    }
}
