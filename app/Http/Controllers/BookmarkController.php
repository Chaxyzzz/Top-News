<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Bookmark;
use App\Services\ArticleEngagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookmarkController extends Controller
{
    public function __construct(
        protected ArticleEngagementService $engagementService
    ) {}

    /**
     * Toggle bookmark on an article for the authenticated reader.
     */
    public function store(Request $request, Article $article): JsonResponse|RedirectResponse
    {
        $user = Auth::user();

        try {
            $result = $this->engagementService->toggleBookmark($user, $article);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'bookmarked' => $result['bookmarked'],
                    'message' => $result['message'],
                ]);
            }

            return redirect()->back()->with('success', $result['message']);
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

    /**
     * Delete an existing bookmark.
     */
    public function destroy(Request $request, Bookmark $bookmark): JsonResponse|RedirectResponse
    {
        $this->authorize('delete', $bookmark);

        $bookmark->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Artikel berhasil dihapus dari daftar simpanan.',
            ]);
        }

        return redirect()->route('account.bookmarks')
            ->with('success', 'Artikel berhasil dihapus dari daftar simpanan.');
    }
}
