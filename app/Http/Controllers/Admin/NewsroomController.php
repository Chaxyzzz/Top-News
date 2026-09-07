<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ArticleStatus;
use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NewsroomController extends Controller
{
    /**
     * Display the Newsroom Editorial Review Queue and Pipeline.
     */
    public function index(Request $request): View
    {
        $currentUser = Auth::user();

        // 1. Articles needing review (Submitted, oldest first)
        $needsReviewArticles = Article::with(['category', 'author'])
            ->where('status', ArticleStatus::Submitted)
            ->oldest('submitted_at')
            ->limit(15)
            ->get();

        // 2. Articles in active review
        $inReviewArticles = Article::with(['category', 'author', 'editor'])
            ->where('status', ArticleStatus::InReview)
            ->latest('review_started_at')
            ->limit(15)
            ->get();

        // 3. Articles requiring author revision
        $revisionArticlesQuery = Article::with(['category', 'author', 'editor'])
            ->where('status', ArticleStatus::RevisionRequested);

        if (! $currentUser->hasPermission('articles.view_all')) {
            $revisionArticlesQuery->where('author_id', $currentUser->id);
        }
        $revisionArticles = $revisionArticlesQuery->latest('updated_at')->limit(15)->get();

        // 4. Approved articles ready to publish/schedule
        $approvedArticles = Article::with(['category', 'author', 'editor'])
            ->where('status', ArticleStatus::Approved)
            ->latest('approved_at')
            ->limit(15)
            ->get();

        // 5. Scheduled articles
        $scheduledArticles = Article::with(['category', 'author'])
            ->where('status', ArticleStatus::Scheduled)
            ->orderBy('scheduled_at')
            ->limit(15)
            ->get();

        // Counts for tab badges
        $counts = [
            'needs_review' => Article::where('status', ArticleStatus::Submitted)->count(),
            'in_review' => Article::where('status', ArticleStatus::InReview)->count(),
            'revision_requested' => Article::where('status', ArticleStatus::RevisionRequested)->count(),
            'approved' => Article::where('status', ArticleStatus::Approved)->count(),
            'scheduled' => Article::where('status', ArticleStatus::Scheduled)->count(),
        ];

        return view('admin.newsroom.index', compact(
            'needsReviewArticles',
            'inReviewArticles',
            'revisionArticles',
            'approvedArticles',
            'scheduledArticles',
            'counts'
        ));
    }
}
