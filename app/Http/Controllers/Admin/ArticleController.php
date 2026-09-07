<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ArticleStatus;
use App\Enums\ArticleType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreArticleRequest;
use App\Http\Requests\Admin\UpdateArticleRequest;
use App\Models\Article;
use App\Models\ArticleDailyStat;
use App\Models\ArticleEditorialAction;
use App\Models\ArticleReaction;
use App\Models\ArticleRevision;
use App\Models\ArticleVideo;
use App\Models\Bookmark;
use App\Models\BreakingNews;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Gallery;
use App\Models\PhotoStory;
use App\Models\Tag;
use App\Models\User;
use App\Services\ArticleWorkflowService;
use App\Services\AuditLogService;
use App\Services\ContentSanitizerService;
use App\Services\PublicContentCacheService;
use App\Services\VideoNewsService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    public function __construct(
        protected ArticleWorkflowService $workflowService,
        protected AuditLogService $auditLog,
        protected PublicContentCacheService $cacheService,
        protected VideoNewsService $videoService
    ) {}

    /**
     * Display a listing of articles.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Article::class);

        $currentUser = Auth::user();
        $query = Article::with(['category', 'author', 'editor', 'tags', 'featuredMedia'])->latest('updated_at');

        // Role-based scoping: Journalists only see their own articles unless they have view_all
        if (! $currentUser->hasPermission('articles.view_all')) {
            $query->where('author_id', $currentUser->id);
        }

        // Filter by Status Tab
        if ($status = $request->query('status')) {
            if ($statusEnum = ArticleStatus::tryFrom($status)) {
                $query->where('status', $statusEnum);
            }
        }

        // Filter by Category
        if ($categoryId = $request->query('category_id')) {
            $query->where('category_id', $categoryId);
        }

        // Filter by Author (for editors/admins)
        if ($authorId = $request->query('author_id')) {
            $query->where('author_id', $authorId);
        }

        // Filter by Content Type
        if ($contentType = $request->query('content_type')) {
            if ($typeEnum = ArticleType::tryFrom($contentType)) {
                $query->where('content_type', $typeEnum);
            }
        }

        // Search in title, subtitle, or excerpt
        if ($search = $request->query('search')) {
            $searchTerm = '%'.trim($search).'%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', $searchTerm)
                    ->orWhere('subtitle', 'like', $searchTerm)
                    ->orWhere('excerpt', 'like', $searchTerm);
            });
        }

        // Get Status Tab Counts efficiently
        $statusCountsQuery = Article::query();
        if (! $currentUser->hasPermission('articles.view_all')) {
            $statusCountsQuery->where('author_id', $currentUser->id);
        }
        $rawStatusCounts = $statusCountsQuery->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $articles = $query->paginate(20)->withQueryString();
        $categories = Category::active()->ordered()->get();
        $authors = $currentUser->hasPermission('articles.view_all')
            ? User::whereHas('roles', fn ($q) => $q->whereIn('name', ['journalist', 'editor', 'editor_in_chief', 'admin', 'super_admin']))->orderBy('name')->get()
            : collect();

        return view('admin.articles.index', compact('articles', 'categories', 'authors', 'rawStatusCounts'));
    }

    /**
     * Show the form for creating a new article.
     */
    public function create(): View
    {
        $this->authorize('create', Article::class);

        $categories = Category::active()->ordered()->get();
        $tags = Tag::orderBy('name')->get();
        $galleries = Gallery::latest()->get();
        $authors = Auth::user()->can('assignAuthor', Article::class)
            ? User::whereHas('roles', fn ($q) => $q->whereIn('name', ['journalist', 'editor', 'editor_in_chief', 'contributor', 'admin', 'super_admin']))->orderBy('name')->get()
            : collect();

        return view('admin.articles.create', compact('categories', 'tags', 'authors', 'galleries'));
    }

    /**
     * Store a newly created article in storage.
     */
    public function store(StoreArticleRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $currentUser = Auth::user();

        // 1. Sanitize HTML Content
        $validated['content'] = ContentSanitizerService::sanitize($validated['content']);

        // 2. Handle Author Attribution
        if (empty($validated['author_id']) || ! $currentUser->can('assignAuthor', Article::class)) {
            $validated['author_id'] = $currentUser->id;
        }

        // 3. Handle Featured Image Upload (Legacy direct upload fallback)
        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('articles', 'public');
            $validated['featured_image'] = $path;
        }

        // 4. Set Default Status
        $validated['status'] = ArticleStatus::Draft;
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_breaking'] = $request->boolean('is_breaking');
        $validated['is_editor_choice'] = $request->boolean('is_editor_choice');
        $validated['is_sponsored'] = $request->boolean('is_sponsored');
        $validated['homepage_priority'] = (int) ($validated['homepage_priority'] ?? 0);
        $validated['allow_comments'] = $request->boolean('allow_comments', true);
        $validated['robots_index'] = $request->boolean('robots_index', true);

        // 5. Create in Database Transaction
        $article = DB::transaction(function () use ($validated, $request, $currentUser) {
            $article = Article::create($validated);

            if ($request->filled('tags')) {
                $article->tags()->sync($request->input('tags'));
            }

            // Handle Video News Metadata
            if ($article->content_type === ArticleType::Video && $request->filled('video_url')) {
                $rawVideo = $request->input('video_url');
                $videoId = $this->videoService->extractYouTubeId($rawVideo);
                if ($videoId) {
                    $article->video()->create([
                        'provider' => 'youtube',
                        'video_id' => $videoId,
                        'embed_url' => $this->videoService->generateEmbedUrl($videoId),
                        'duration_seconds' => $request->input('duration_seconds'),
                    ]);
                }
            }

            // Handle Photo Story Gallery
            if ($article->content_type === ArticleType::PhotoStory && $request->filled('gallery_id')) {
                $article->photoStory()->create([
                    'gallery_id' => $request->input('gallery_id'),
                ]);
            }

            // Create initial revision snapshot
            $this->workflowService->createRevisionSnapshot($article, $currentUser, 'Draf awal dibuat');

            $this->auditLog->log('article.created', $article, "Artikel baru '{$article->title}' dibuat oleh {$currentUser->name}.", $currentUser);

            return $article;
        });

        return redirect()->route('admin.articles.edit', $article)
            ->with('success', "Draf artikel '{$article->title}' berhasil disimpan.");
    }

    /**
     * Display the specified article (Admin Read-Only Review & Timeline View).
     */
    public function show(Article $article): View
    {
        $this->authorize('view', $article);

        $article->load(['category', 'author', 'editor', 'tags', 'featuredMedia', 'video', 'photoStory.gallery', 'revisions.user', 'editorialActions.user']);

        return view('admin.articles.show', compact('article'));
    }

    /**
     * Show the form for editing the specified article.
     */
    public function edit(Article $article): View
    {
        $this->authorize('update', $article);

        $article->load(['featuredMedia', 'video', 'photoStory.gallery']);
        $categories = Category::ordered()->get();
        $tags = Tag::orderBy('name')->get();
        $galleries = Gallery::latest()->get();
        $authors = Auth::user()->can('assignAuthor', Article::class)
            ? User::whereHas('roles', fn ($q) => $q->whereIn('name', ['journalist', 'editor', 'editor_in_chief', 'contributor', 'admin', 'super_admin']))->orderBy('name')->get()
            : collect();

        $selectedTags = $article->tags->pluck('id')->toArray();

        return view('admin.articles.edit', compact('article', 'categories', 'tags', 'authors', 'selectedTags', 'galleries'));
    }

    /**
     * Update the specified article in storage.
     */
    public function update(UpdateArticleRequest $request, Article $article): RedirectResponse
    {
        $validated = $request->validated();
        $currentUser = Auth::user();

        // 1. Sanitize HTML Content
        $validated['content'] = ContentSanitizerService::sanitize($validated['content']);

        // 2. Author Reassignment (Only if permitted)
        if (! $currentUser->can('assignAuthor', Article::class)) {
            unset($validated['author_id']);
        }

        // 3. Prevent Slug changes on Published articles unless permitted
        if ($article->status === ArticleStatus::Published && ! $currentUser->hasPermission('articles.change_published_slug')) {
            unset($validated['slug']);
        }

        // 4. Handle Featured Image Upload (Legacy direct upload fallback)
        if ($request->hasFile('featured_image')) {
            // Delete old file if present
            if ($article->featured_image && Storage::disk('public')->exists($article->featured_image)) {
                Storage::disk('public')->delete($article->featured_image);
            }
            $path = $request->file('featured_image')->store('articles', 'public');
            $validated['featured_image'] = $path;
        }

        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_breaking'] = $request->boolean('is_breaking');
        $validated['is_editor_choice'] = $request->boolean('is_editor_choice');
        $validated['is_sponsored'] = $request->boolean('is_sponsored');
        $validated['homepage_priority'] = (int) ($validated['homepage_priority'] ?? 0);
        $validated['allow_comments'] = $request->boolean('allow_comments', true);
        $validated['robots_index'] = $request->boolean('robots_index', true);

        // 5. Update in Database Transaction
        DB::transaction(function () use ($article, $validated, $request, $currentUser) {
            $article->update($validated);

            if ($request->has('tags')) {
                $article->tags()->sync($request->input('tags', []));
            }

            // Handle Video News Metadata
            if ($article->content_type === ArticleType::Video) {
                if ($request->filled('video_url')) {
                    $rawVideo = $request->input('video_url');
                    $videoId = $this->videoService->extractYouTubeId($rawVideo);
                    if ($videoId) {
                        $article->video()->updateOrCreate(
                            ['article_id' => $article->id],
                            [
                                'provider' => 'youtube',
                                'video_id' => $videoId,
                                'embed_url' => $this->videoService->generateEmbedUrl($videoId),
                                'duration_seconds' => $request->input('duration_seconds'),
                            ]
                        );
                    }
                }
            }

            // Handle Photo Story Gallery
            if ($article->content_type === ArticleType::PhotoStory) {
                if ($request->filled('gallery_id')) {
                    $article->photoStory()->updateOrCreate(
                        ['article_id' => $article->id],
                        ['gallery_id' => $request->input('gallery_id')]
                    );
                }
            }

            // Create revision snapshot on content update
            $this->workflowService->createRevisionSnapshot($article, $currentUser, 'Pembaruan naskah artikel');

            $this->auditLog->log('article.updated', $article, "Artikel '{$article->title}' diperbarui oleh {$currentUser->name}.", $currentUser);
            $this->cacheService->invalidateHomepage();
        });

        return redirect()->route('admin.articles.edit', $article)
            ->with('success', "Artikel '{$article->title}' berhasil diperbarui.");
    }

    /**
     * Remove the specified article from storage (Permanent Delete).
     */
    public function destroy(Article $article): RedirectResponse
    {
        $this->authorize('delete', $article);

        $title = $article->title;
        $currentUser = Auth::user();

        DB::transaction(function () use ($article, $currentUser, $title) {
            // Detach pivot relations
            $article->tags()->detach();
            $article->curatedInSections()->detach();
            DB::table('homepage_section_articles')->where('article_id', $article->id)->delete();

            // Delete article-owned child rows
            ArticleDailyStat::where('article_id', $article->id)->delete();
            ArticleRevision::where('article_id', $article->id)->delete();
            ArticleEditorialAction::where('article_id', $article->id)->delete();
            Bookmark::where('article_id', $article->id)->delete();
            ArticleReaction::where('article_id', $article->id)->delete();
            Comment::where('article_id', $article->id)->forceDelete();
            ArticleVideo::where('article_id', $article->id)->delete();
            PhotoStory::where('article_id', $article->id)->delete();
            BreakingNews::where('article_id', $article->id)->delete();

            $article->forceDelete();

            $this->auditLog->log('article.permanently_deleted', $article, "Artikel '{$title}' dihapus permanen oleh {$currentUser->name}.", $currentUser);
            $this->cacheService->invalidateHomepage();
        });

        return redirect()->route('admin.articles.index')
            ->with('success', "Artikel '{$title}' berhasil dihapus permanen.");
    }
}
