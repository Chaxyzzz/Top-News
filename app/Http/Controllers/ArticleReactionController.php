<?php

namespace App\Http\Controllers;

use App\Enums\ReactionType;
use App\Http\Requests\StoreReactionRequest;
use App\Models\Article;
use App\Services\ArticleEngagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ArticleReactionController extends Controller
{
    public function __construct(
        protected ArticleEngagementService $engagementService
    ) {}

    /**
     * Store or toggle reaction on a published article.
     */
    public function store(StoreReactionRequest $request, Article $article): JsonResponse|RedirectResponse
    {
        $user = Auth::user();
        $reactionType = ReactionType::from($request->input('reaction'));

        try {
            $result = $this->engagementService->toggleReaction($user, $article, $reactionType);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'reacted' => $result['reacted'],
                    'current_reaction' => $result['current_reaction'],
                    'counts' => $result['counts'],
                    'message' => $result['reacted']
                        ? "Respon '{$reactionType->label()}' berhasil dicatat."
                        : 'Respon Anda berhasil dibatalkan.',
                ]);
            }

            return redirect()->back();
        } catch (\InvalidArgumentException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
