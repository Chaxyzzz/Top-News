<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Services\CommentModerationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CommentModerationController extends Controller
{
    public function __construct(
        protected CommentModerationService $moderationService
    ) {}

    /**
     * Display list of comments for moderation.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Comment::class);

        $comments = $this->moderationService->getModerationList($request, 20);

        // Calculate counts for status tabs
        $counts = [
            'all' => DB::table('comments')->whereNull('deleted_at')->count(),
            'pending' => DB::table('comments')->where('status', 'pending')->whereNull('deleted_at')->count(),
            'approved' => DB::table('comments')->where('status', 'approved')->whereNull('deleted_at')->count(),
            'rejected' => DB::table('comments')->where('status', 'rejected')->whereNull('deleted_at')->count(),
            'spam' => DB::table('comments')->where('status', 'spam')->whereNull('deleted_at')->count(),
        ];

        return view('admin.comments.index', compact('comments', 'counts'));
    }

    /**
     * Approve a comment.
     */
    public function approve(Comment $comment): RedirectResponse
    {
        $this->authorize('approve', $comment);

        $this->moderationService->approve($comment, Auth::user());

        return redirect()->back()->with('success', "Komentar #{$comment->id} berhasil disetujui untuk ditampilkan ke publik.");
    }

    /**
     * Reject a comment.
     */
    public function reject(Request $request, Comment $comment): RedirectResponse
    {
        $this->authorize('reject', $comment);

        $reason = $request->input('reason');
        $this->moderationService->reject($comment, Auth::user(), $reason);

        return redirect()->back()->with('success', "Komentar #{$comment->id} berhasil ditolak.");
    }

    /**
     * Mark a comment as spam.
     */
    public function spam(Request $request, Comment $comment): RedirectResponse
    {
        $this->authorize('markSpam', $comment);

        $reason = $request->input('reason', 'Ditandai sebagai spam');
        $this->moderationService->markSpam($comment, Auth::user(), $reason);

        return redirect()->back()->with('success', "Komentar #{$comment->id} ditandai sebagai spam.");
    }

    /**
     * Delete a comment permanently or soft delete.
     */
    public function destroy(Comment $comment): RedirectResponse
    {
        $this->authorize('delete', $comment);

        $this->moderationService->deleteComment($comment, Auth::user());

        return redirect()->back()->with('success', "Komentar #{$comment->id} berhasil dihapus.");
    }
}
