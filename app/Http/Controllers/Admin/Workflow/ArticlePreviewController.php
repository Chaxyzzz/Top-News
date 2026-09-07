<?php

namespace App\Http\Controllers\Admin\Workflow;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Services\ContentSanitizerService;
use App\Services\SeoService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class ArticlePreviewController extends Controller
{
    /**
     * Display a secure preview of an unpublished or draft article.
     */
    public function preview(Request $request, Article $article): View
    {
        // If not a signed URL, ensure the current authenticated user has preview permission
        if (! $request->hasValidSignature()) {
            if (! $request->user() || ! $request->user()->can('preview', $article)) {
                abort(403, 'Akses pratinjau ditolak atau tautan verifikasi telah kedaluwarsa.');
            }
        }

        $article->load(['category', 'author', 'tags']);
        $sanitizedContent = ContentSanitizerService::sanitize($article->content);
        $isPreview = true;
        $seoData = app(SeoService::class)->forArticle($article);
        $seoData->robots = 'noindex,nofollow';

        return view('pages.preview', compact('article', 'sanitizedContent', 'isPreview', 'seoData'));
    }

    /**
     * Generate a temporary signed preview URL (valid for 30 minutes).
     */
    public function generateSignedUrl(Request $request, Article $article): JsonResponse
    {
        $this->authorize('preview', $article);

        $signedUrl = URL::temporarySignedRoute(
            'articles.preview.signed',
            now()->addMinutes(30),
            ['article' => $article->id]
        );

        return response()->json([
            'status' => 'success',
            'signed_url' => $signedUrl,
            'expires_at' => now()->addMinutes(30)->toIso8601String(),
        ]);
    }
}
