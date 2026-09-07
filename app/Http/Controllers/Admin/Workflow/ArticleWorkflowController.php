<?php

namespace App\Http\Controllers\Admin\Workflow;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RevisionRequestRequest;
use App\Http\Requests\Admin\ScheduleArticleRequest;
use App\Models\Article;
use App\Services\ArticleWorkflowService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArticleWorkflowController extends Controller
{
    public function __construct(
        protected ArticleWorkflowService $workflowService
    ) {}

    /**
     * Submit draft for editorial review.
     */
    public function submit(Article $article): RedirectResponse
    {
        $this->authorize('submit', $article);

        $this->workflowService->submit($article, Auth::user());

        return back()->with('success', "Artikel '{$article->title}' berhasil diajukan ke meja redaksi.");
    }

    /**
     * Start reviewing an article.
     */
    public function startReview(Article $article): RedirectResponse
    {
        $this->authorize('startReview', $article);

        $this->workflowService->startReview($article, Auth::user());

        return back()->with('success', "Proses peninjauan artikel '{$article->title}' telah dimulai.");
    }

    /**
     * Request revisions from author.
     */
    public function requestRevision(RevisionRequestRequest $request, Article $article): RedirectResponse
    {
        $this->authorize('requestRevision', $article);

        $this->workflowService->requestRevision($article, Auth::user(), $request->input('note'));

        return back()->with('warning', "Permintaan revisi untuk '{$article->title}' telah dikirimkan ke penulis.");
    }

    /**
     * Approve article.
     */
    public function approve(Article $article): RedirectResponse
    {
        $this->authorize('approve', $article);

        $this->workflowService->approve($article, Auth::user());

        return back()->with('success', "Artikel '{$article->title}' telah disetujui untuk diterbitkan.");
    }

    /**
     * Publish article immediately.
     */
    public function publish(Article $article): RedirectResponse
    {
        $this->authorize('publish', $article);

        $this->workflowService->publish($article, Auth::user());

        return back()->with('success', "Artikel '{$article->title}' resmi diterbitkan ke portal publik TopNews.");
    }

    /**
     * Schedule article for future publication.
     */
    public function schedule(ScheduleArticleRequest $request, Article $article): RedirectResponse
    {
        $this->authorize('schedule', $article);

        $scheduledAt = Carbon::parse($request->input('scheduled_at'));
        $this->workflowService->schedule($article, Auth::user(), $scheduledAt);

        $formattedTime = $scheduledAt->format('d/m/Y H:i');

        return back()->with('success', "Artikel '{$article->title}' dijadwalkan terbit pada {$formattedTime} WIB.");
    }

    /**
     * Unpublish an article.
     */
    public function unpublish(Request $request, Article $article): RedirectResponse
    {
        $this->authorize('unpublish', $article);

        $reason = $request->input('reason');
        $this->workflowService->unpublish($article, Auth::user(), $reason);

        return back()->with('warning', "Artikel '{$article->title}' ditarik dari publikasi.");
    }

    /**
     * Archive an article.
     */
    public function archive(Article $article): RedirectResponse
    {
        $this->authorize('archive', $article);

        $this->workflowService->archive($article, Auth::user());

        return back()->with('info', "Artikel '{$article->title}' berhasil diarsipkan.");
    }

    /**
     * Restore article from archive.
     */
    public function restore(Article $article): RedirectResponse
    {
        $this->authorize('restore', $article);

        $this->workflowService->restoreFromArchive($article, Auth::user());

        return back()->with('success', "Artikel '{$article->title}' dipulihkan ke status draf.");
    }
}
