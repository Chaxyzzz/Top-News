<?php

namespace App\Services;

use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\ArticleEditorialAction;
use App\Models\ArticleRevision;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ArticleWorkflowService
{
    public function __construct(
        protected AuditLogService $auditLog,
        protected PublicContentCacheService $cacheService
    ) {}

    /**
     * Submit draft for review.
     */
    public function submit(Article $article, User $actor): bool
    {
        if ($article->status !== ArticleStatus::Draft && $article->status !== ArticleStatus::RevisionRequested) {
            throw new InvalidArgumentException("Artikel dengan status {$article->status->label()} tidak dapat diajukan untuk review.");
        }

        return DB::transaction(function () use ($article, $actor) {
            $fromStatus = $article->status->value;
            $article->update([
                'status' => ArticleStatus::Submitted,
                'submitted_at' => now(),
            ]);

            $this->logAction($article, $actor, 'submitted', $fromStatus, ArticleStatus::Submitted->value, 'Artikel diajukan ke antrean meja redaksi untuk ditinjau.');
            $this->createRevisionSnapshot($article, $actor, 'Pengajuan naskah untuk ditinjau');

            $this->auditLog->log('article.submitted', $article, "Artikel '{$article->title}' diajukan untuk review oleh {$actor->name}.", $actor);

            return true;
        });
    }

    /**
     * Start editorial review.
     */
    public function startReview(Article $article, User $actor): bool
    {
        if ($article->status !== ArticleStatus::Submitted) {
            throw new InvalidArgumentException('Hanya artikel berstatus Menunggu Review yang dapat dimulai proses peninjauannya.');
        }

        return DB::transaction(function () use ($article, $actor) {
            $fromStatus = $article->status->value;
            $article->update([
                'status' => ArticleStatus::InReview,
                'editor_id' => $actor->id,
                'review_started_at' => now(),
            ]);

            $this->logAction($article, $actor, 'review_started', $fromStatus, ArticleStatus::InReview->value, 'Editor mulai meninjau naskah artikel.');
            $this->auditLog->log('article.review_started', $article, "Peninjauan artikel '{$article->title}' dimulai oleh Editor {$actor->name}.", $actor);

            return true;
        });
    }

    /**
     * Request revisions from author.
     */
    public function requestRevision(Article $article, User $actor, string $note): bool
    {
        if ($article->status !== ArticleStatus::InReview && $article->status !== ArticleStatus::Submitted) {
            throw new InvalidArgumentException('Permintaan revisi hanya dapat dilakukan pada artikel yang sedang ditinjau.');
        }

        if (empty(trim($note))) {
            throw new InvalidArgumentException('Catatan revisi wajib diisi oleh editor.');
        }

        return DB::transaction(function () use ($article, $actor, $note) {
            $fromStatus = $article->status->value;
            $article->update([
                'status' => ArticleStatus::RevisionRequested,
            ]);

            $this->logAction($article, $actor, 'revision_requested', $fromStatus, ArticleStatus::RevisionRequested->value, $note);
            $this->auditLog->log('article.revision_requested', $article, "Editor {$actor->name} meminta revisi untuk artikel '{$article->title}'.", $actor, ['note' => $note]);

            return true;
        });
    }

    /**
     * Approve article for publication.
     */
    public function approve(Article $article, User $actor): bool
    {
        if ($article->status !== ArticleStatus::InReview && $article->status !== ArticleStatus::Submitted) {
            throw new InvalidArgumentException('Hanya artikel dalam proses peninjauan yang dapat disetujui.');
        }

        return DB::transaction(function () use ($article, $actor) {
            $fromStatus = $article->status->value;
            $article->update([
                'status' => ArticleStatus::Approved,
                'editor_id' => $actor->id,
                'approved_at' => now(),
            ]);

            $this->logAction($article, $actor, 'approved', $fromStatus, ArticleStatus::Approved->value, 'Naskah artikel disetujui dan siap diterbitkan.');
            $this->auditLog->log('article.approved', $article, "Artikel '{$article->title}' disetujui oleh Editor {$actor->name}.", $actor);

            return true;
        });
    }

    /**
     * Publish article immediately.
     */
    public function publish(Article $article, User $actor): bool
    {
        return DB::transaction(function () use ($article, $actor) {
            $fromStatus = $article->status->value;
            $article->update([
                'status' => ArticleStatus::Published,
                'published_at' => now(),
                'scheduled_at' => null,
            ]);

            $this->logAction($article, $actor, 'published', $fromStatus, ArticleStatus::Published->value, 'Artikel resmi diterbitkan ke portal publik TopNews.');
            $this->createRevisionSnapshot($article, $actor, 'Publikasi resmi artikel');
            $this->auditLog->log('article.published', $article, "Artikel '{$article->title}' diterbitkan oleh {$actor->name}.", $actor);
            $this->cacheService->invalidateHomepage();

            return true;
        });
    }

    /**
     * Schedule article for future publication.
     */
    public function schedule(Article $article, User $actor, CarbonInterface $scheduledAt): bool
    {
        if ($scheduledAt->isPast()) {
            throw new InvalidArgumentException('Waktu penjadwalan publikasi harus berada di masa mendatang.');
        }

        return DB::transaction(function () use ($article, $actor, $scheduledAt) {
            $fromStatus = $article->status->value;
            $article->update([
                'status' => ArticleStatus::Scheduled,
                'scheduled_at' => $scheduledAt,
            ]);

            $formattedDate = $scheduledAt->format('d/m/Y H:i');
            $this->logAction($article, $actor, 'scheduled', $fromStatus, ArticleStatus::Scheduled->value, "Artikel dijadwalkan terbit pada {$formattedDate} WIB.");
            $this->auditLog->log('article.scheduled', $article, "Artikel '{$article->title}' dijadwalkan terbit pada {$formattedDate} WIB oleh {$actor->name}.", $actor);
            $this->cacheService->invalidateHomepage();

            return true;
        });
    }

    /**
     * Unpublish an article.
     */
    public function unpublish(Article $article, User $actor, ?string $reason = null): bool
    {
        if ($article->status !== ArticleStatus::Published) {
            throw new InvalidArgumentException('Hanya artikel yang sudah terbit yang dapat ditarik kembali.');
        }

        return DB::transaction(function () use ($article, $actor, $reason) {
            $fromStatus = $article->status->value;
            $article->update([
                'status' => ArticleStatus::Approved,
            ]);

            $note = $reason ? "Penarikan publikasi: {$reason}" : 'Artikel ditarik dari publikasi kembali ke status Disetujui.';
            $this->logAction($article, $actor, 'unpublished', $fromStatus, ArticleStatus::Approved->value, $note);
            $this->auditLog->log('article.unpublished', $article, "Artikel '{$article->title}' ditarik dari publikasi oleh {$actor->name}.", $actor, ['reason' => $reason]);
            $this->cacheService->invalidateHomepage();

            return true;
        });
    }

    /**
     * Archive an article.
     */
    public function archive(Article $article, User $actor): bool
    {
        return DB::transaction(function () use ($article, $actor) {
            $fromStatus = $article->status->value;
            $article->update([
                'status' => ArticleStatus::Archived,
                'archived_at' => now(),
            ]);

            $this->logAction($article, $actor, 'archived', $fromStatus, ArticleStatus::Archived->value, 'Artikel dipindahkan ke arsip.');
            $this->auditLog->log('article.archived', $article, "Artikel '{$article->title}' diarsipkan oleh {$actor->name}.", $actor);
            $this->cacheService->invalidateHomepage();

            return true;
        });
    }

    /**
     * Restore article from archive.
     */
    public function restoreFromArchive(Article $article, User $actor): bool
    {
        if ($article->status !== ArticleStatus::Archived) {
            throw new InvalidArgumentException('Hanya artikel terarsip yang dapat dipulihkan.');
        }

        return DB::transaction(function () use ($article, $actor) {
            $fromStatus = $article->status->value;
            $article->update([
                'status' => ArticleStatus::Draft,
                'archived_at' => null,
            ]);

            $this->logAction($article, $actor, 'restored', $fromStatus, ArticleStatus::Draft->value, 'Artikel dipulihkan dari arsip ke draf.');
            $this->auditLog->log('article.restored', $article, "Artikel '{$article->title}' dipulihkan dari arsip ke draf oleh {$actor->name}.", $actor);

            return true;
        });
    }

    /**
     * Create snapshot revision of article state.
     */
    public function createRevisionSnapshot(Article $article, User $actor, ?string $note = null): ArticleRevision
    {
        $lastRevisionNumber = (int) ArticleRevision::where('article_id', $article->id)->max('revision_number');
        $newRevisionNumber = $lastRevisionNumber + 1;

        return ArticleRevision::create([
            'article_id' => $article->id,
            'user_id' => $actor->id,
            'revision_number' => $newRevisionNumber,
            'title' => $article->title,
            'subtitle' => $article->subtitle,
            'excerpt' => $article->excerpt,
            'content' => $article->content,
            'category_id' => $article->category_id,
            'content_type' => $article->content_type->value,
            'featured_image' => $article->featured_image,
            'metadata' => [
                'note' => $note,
                'status' => $article->status->value,
                'tags' => $article->tags->pluck('name')->toArray(),
            ],
            'created_at' => now(),
        ]);
    }

    /**
     * Restore article to a historical revision snapshot.
     */
    public function restoreRevision(Article $article, ArticleRevision $revision, User $actor): bool
    {
        return DB::transaction(function () use ($article, $revision, $actor) {
            // First snapshot current state before restoring
            $this->createRevisionSnapshot($article, $actor, "Snapshot sebelum memulihkan revisi #{$revision->revision_number}");

            $article->update([
                'title' => $revision->title,
                'subtitle' => $revision->subtitle,
                'excerpt' => $revision->excerpt,
                'content' => $revision->content,
                'category_id' => $revision->category_id ?? $article->category_id,
                'featured_image' => $revision->featured_image ?? $article->featured_image,
            ]);

            $this->logAction($article, $actor, 'revision_restored', $article->status->value, $article->status->value, "Artikel dipulihkan ke versi revisi #{$revision->revision_number}.");
            $this->auditLog->log('article.revision_restored', $article, "Artikel '{$article->title}' dipulihkan ke versi revisi #{$revision->revision_number} oleh {$actor->name}.", $actor);

            return true;
        });
    }

    /**
     * Log internal editorial action.
     */
    protected function logAction(Article $article, User $actor, string $action, ?string $fromStatus, ?string $toStatus, ?string $note = null): ArticleEditorialAction
    {
        return ArticleEditorialAction::create([
            'article_id' => $article->id,
            'user_id' => $actor->id,
            'action' => $action,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'note' => $note,
            'created_at' => now(),
        ]);
    }
}
