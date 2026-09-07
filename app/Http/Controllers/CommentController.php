<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Article;
use App\Models\Comment;
use App\Services\CommentModerationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function __construct(
        protected CommentModerationService $commentService
    ) {}

    /**
     * Submit a new comment or reply for an article.
     */
    public function store(StoreCommentRequest $request, Article $article): JsonResponse|RedirectResponse
    {
        $user = Auth::user();

        try {
            $comment = $this->commentService->createComment(
                $user,
                $article,
                $request->input('body'),
                $request->input('parent_id') ? (int) $request->input('parent_id') : null
            );

            $message = 'Komentar Anda telah berhasil dikirim dan sedang menunggu peninjauan oleh tim moderasi redaksi.';

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'comment' => [
                        'id' => $comment->id,
                        'uuid' => $comment->uuid,
                        'status' => $comment->status->value,
                    ],
                ]);
            }

            return redirect()->to(route('news.show', $article->slug).'#comments')
                ->with('success', $message);
        } catch (\InvalidArgumentException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return redirect()->to(route('news.show', $article->slug).'#comments')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Delete reader's own comment.
     */
    public function destroy(Request $request, Comment $comment): RedirectResponse
    {
        $this->authorize('delete', $comment);

        $this->commentService->deleteComment($comment, Auth::user());

        return redirect()->back()->with('success', 'Komentar Anda telah dihapus.');
    }
}
