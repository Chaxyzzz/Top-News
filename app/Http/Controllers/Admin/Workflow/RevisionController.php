<?php

namespace App\Http\Controllers\Admin\Workflow;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleRevision;
use App\Services\ArticleWorkflowService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class RevisionController extends Controller
{
    public function __construct(
        protected ArticleWorkflowService $workflowService
    ) {}

    /**
     * Display revision history for the article.
     */
    public function index(Article $article): View
    {
        $this->authorize('view', $article);

        $revisions = $article->revisions()->with('user')->get();

        return view('admin.articles.revisions', compact('article', 'revisions'));
    }

    /**
     * Restore the article to a specific revision snapshot.
     */
    public function restore(Article $article, ArticleRevision $revision): RedirectResponse
    {
        $this->authorize('update', $article);

        if ($revision->article_id !== $article->id) {
            abort(404);
        }

        $this->workflowService->restoreRevision($article, $revision, Auth::user());

        return redirect()->route('admin.articles.edit', $article)
            ->with('success', "Artikel berhasil dipulihkan ke versi revisi #{$revision->revision_number}.");
    }
}
