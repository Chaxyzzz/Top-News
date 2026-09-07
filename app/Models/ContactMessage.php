<?php

namespace App\Models;

use App\Enums\ContactMessageStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ContactMessage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'name',
        'email',
        'subject',
        'message',
        'category',
        'status',
        'ip_hash',
        'user_agent_summary',
        'assigned_to',
        'read_at',
        'resolved_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => ContactMessageStatus::class,
        'read_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    /**
     * Bootstrap the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (ContactMessage $msg) {
            if (empty($msg->uuid)) {
                $msg->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Assigned staff user.
     *
     * @return BelongsTo<User, $this>
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Scope to new messages.
     */
    public function scopeNewMessages(Builder $query): Builder
    {
        return $query->where('status', ContactMessageStatus::New);
    }

    /**
     * Scope to active/unresolved messages (new, read, in_progress).
     */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereIn('status', [
            ContactMessageStatus::New,
            ContactMessageStatus::Read,
            ContactMessageStatus::InProgress,
        ]);
    }
}
