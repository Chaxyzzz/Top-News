<?php

namespace App\Models;

use App\Enums\UserStatus;
use App\Traits\HasRolesAndPermissions;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRolesAndPermissions, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'name',
        'username',
        'email',
        'password',
        'avatar',
        'phone',
        'public_title',
        'bio',
        'show_on_editorial_team',
        'editorial_team_order',
        'status',
        'account_type',
        'last_login_at',
        'last_login_ip',
        'last_login_user_agent',
        'created_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => UserStatus::Active->value,
        'account_type' => 'staff',
    ];

    /**
     * Bootstrap the model and its traits.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (User $user) {
            if (empty($user->uuid)) {
                $user->uuid = (string) Str::uuid();
            }
            if (! empty($user->username)) {
                $user->username = Str::lower(trim($user->username));
            }
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'status' => UserStatus::class,
            'show_on_editorial_team' => 'boolean',
            'editorial_team_order' => 'integer',
        ];
    }

    /**
     * Articles authored by this user.
     *
     * @return HasMany<Article, $this>
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'author_id');
    }

    /**
     * Alias for articles authored by this user.
     *
     * @return HasMany<Article, $this>
     */
    public function authoredArticles(): HasMany
    {
        return $this->hasMany(Article::class, 'author_id');
    }

    /**
     * Contact messages assigned to this user.
     *
     * @return HasMany<ContactMessage, $this>
     */
    public function assignedContactMessages(): HasMany
    {
        return $this->hasMany(ContactMessage::class, 'assigned_to');
    }

    /**
     * Scope to active editorial team members shown publicly.
     */
    public function scopeEditorialTeam(Builder $query): Builder
    {
        return $query->where('status', UserStatus::Active)
            ->where('show_on_editorial_team', true)
            ->orderBy('editorial_team_order', 'asc')
            ->orderBy('name', 'asc');
    }

    /**
     * User who created this account.
     *
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check if user is active.
     */
    public function isActive(): bool
    {
        return $this->status === UserStatus::Active;
    }

    /**
     * Check if user is suspended.
     */
    public function isSuspended(): bool
    {
        return $this->status === UserStatus::Suspended;
    }

    /**
     * Check if user is a public reader account.
     */
    public function isReader(): bool
    {
        return $this->account_type === 'reader';
    }

    /**
     * Check if user is a staff account.
     */
    public function isStaff(): bool
    {
        return $this->account_type === 'staff';
    }

    /**
     * User's bookmarks.
     *
     * @return HasMany<Bookmark>
     */
    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    /**
     * Articles bookmarked by user.
     *
     * @return BelongsToMany<Article>
     */
    public function bookmarkedArticles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'bookmarks')
            ->withPivot('created_at');
    }

    /**
     * Comments posted by user.
     *
     * @return HasMany<Comment>
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Reactions made by user.
     *
     * @return HasMany<ArticleReaction>
     */
    public function reactions(): HasMany
    {
        return $this->hasMany(ArticleReaction::class);
    }

    /**
     * Get avatar URL or fallback.
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->avatar) {
            return asset('storage/'.$this->avatar);
        }

        return null;
    }

    /**
     * Get initials for avatar fallback (e.g. ZM).
     */
    public function getInitialsAttribute(): string
    {
        $words = explode(' ', trim($this->name));
        $initials = '';

        foreach (array_slice($words, 0, 2) as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }

        return $initials ?: 'U';
    }

    /**
     * Scope query to active users.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', UserStatus::Active);
    }

    /**
     * Scope query to reader users.
     */
    public function scopeReaders(Builder $query): Builder
    {
        return $query->where('account_type', 'reader');
    }

    /**
     * Scope query to staff users.
     */
    public function scopeStaff(Builder $query): Builder
    {
        return $query->where('account_type', 'staff');
    }
}
