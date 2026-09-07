<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBreakingNewsRequest;
use App\Http\Requests\UpdateBreakingNewsRequest;
use App\Models\Article;
use App\Models\BreakingNews;
use App\Services\BreakingNewsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BreakingNewsController extends Controller
{
    public function __construct(
        protected BreakingNewsService $breakingService
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', BreakingNews::class);

        $query = BreakingNews::with(['article:id,title,slug,status,published_at', 'author:id,name']);

        if ($request->filled('status')) {
            if ($request->input('status') === 'active') {
                $query->where('is_active', true);
            } elseif ($request->input('status') === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $breakingNews = $query->orderByDesc('priority')
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.breaking-news.index', compact('breakingNews'));
    }

    public function create(): View
    {
        $this->authorize('create', BreakingNews::class);

        $recentArticles = Article::published()->latest('published_at')->limit(30)->get();

        return view('admin.breaking-news.create', compact('recentArticles'));
    }

    public function store(StoreBreakingNewsRequest $request): RedirectResponse
    {
        $this->authorize('create', BreakingNews::class);

        $validated = $request->validated();
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['priority'] = $validated['priority'] ?? 0;
        $validated['created_by'] = $request->user()->id;

        $breaking = BreakingNews::create($validated);
        $this->breakingService->clearCache();

        return redirect()->route('admin.breaking-news.index')
            ->with('success', 'Breaking news berhasil ditambahkan.');
    }

    public function edit(BreakingNews $breakingNews): View
    {
        $this->authorize('update', $breakingNews);

        $recentArticles = Article::published()->latest('published_at')->limit(30)->get();

        return view('admin.breaking-news.edit', [
            'breaking' => $breakingNews,
            'recentArticles' => $recentArticles,
        ]);
    }

    public function update(UpdateBreakingNewsRequest $request, BreakingNews $breakingNews): RedirectResponse
    {
        $this->authorize('update', $breakingNews);

        $validated = $request->validated();
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['priority'] = $validated['priority'] ?? 0;
        $validated['updated_by'] = $request->user()->id;

        $breakingNews->update($validated);
        $this->breakingService->clearCache();

        return redirect()->route('admin.breaking-news.index')
            ->with('success', 'Breaking news berhasil diperbarui.');
    }

    public function toggleActive(BreakingNews $breakingNews): RedirectResponse
    {
        $this->authorize('update', $breakingNews);

        $breakingNews->is_active = ! $breakingNews->is_active;
        $breakingNews->save();
        $this->breakingService->clearCache();

        $statusText = $breakingNews->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Breaking news berhasil {$statusText}.");
    }

    public function destroy(BreakingNews $breakingNews): RedirectResponse
    {
        $this->authorize('delete', $breakingNews);

        $breakingNews->delete();
        $this->breakingService->clearCache();

        return redirect()->route('admin.breaking-news.index')
            ->with('success', 'Breaking news berhasil dihapus.');
    }
}
