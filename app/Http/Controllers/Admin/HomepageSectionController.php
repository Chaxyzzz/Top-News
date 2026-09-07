<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateHomepageSectionRequest;
use App\Models\Article;
use App\Models\Category;
use App\Models\HomepageSection;
use App\Services\HomepageConfigurationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomepageSectionController extends Controller
{
    public function __construct(
        protected HomepageConfigurationService $configService
    ) {}

    public function index(): View
    {
        $this->authorize('viewAny', HomepageSection::class);

        $sections = $this->configService->getAllSections();

        return view('admin.homepage.index', compact('sections'));
    }

    public function edit(HomepageSection $section): View
    {
        $this->authorize('update', $section);

        $categories = Category::active()->orderBy('name')->get();

        return view('admin.homepage.edit', compact('section', 'categories'));
    }

    public function update(UpdateHomepageSectionRequest $request, HomepageSection $section): RedirectResponse
    {
        $this->authorize('update', $section);

        $validated = $request->validated();
        $validated['is_active'] = $request->boolean('is_active');
        $validated['updated_by'] = $request->user()->id;

        $this->configService->updateSection($section, $validated);

        return redirect()->route('admin.homepage.index')
            ->with('success', "Bagian homepage '{$section->title}' berhasil diperbarui.");
    }

    public function toggleActive(HomepageSection $section): RedirectResponse
    {
        $this->authorize('update', $section);

        $isActive = $this->configService->toggleActive($section);
        $statusText = $isActive ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Bagian '{$section->title}' berhasil {$statusText}.");
    }

    public function reorder(Request $request): RedirectResponse
    {
        $this->authorize('create', HomepageSection::class);

        $orderMap = $request->input('order', []);
        $this->configService->reorderSections($orderMap);

        return back()->with('success', 'Urutan bagian homepage berhasil diperbarui.');
    }

    public function curate(Request $request, HomepageSection $section): View
    {
        $this->authorize('curate', $section);

        $curatedArticles = $section->curatedArticles()
            ->with(['category:id,name', 'author:id,name'])
            ->get();

        // Search candidate published articles for curation
        $search = $request->input('search');
        $categoryId = $request->input('category_id');

        $candidateQuery = Article::published()
            ->with(['category:id,name', 'author:id,name'])
            ->latest('published_at');

        if ($search) {
            $candidateQuery->where('title', 'like', "%{$search}%");
        }
        if ($categoryId) {
            $candidateQuery->where('category_id', $categoryId);
        }

        $candidates = $candidateQuery->paginate(15)->withQueryString();
        $categories = Category::active()->orderBy('name')->get();

        return view('admin.homepage.curate', compact('section', 'curatedArticles', 'candidates', 'categories'));
    }

    public function updateCurate(Request $request, HomepageSection $section): RedirectResponse
    {
        $this->authorize('curate', $section);

        $articleIds = $request->input('article_ids', []);
        $this->configService->curateArticles($section, $articleIds);

        return redirect()->route('admin.homepage.index')
            ->with('success', "Kurasi berita untuk bagian '{$section->title}' berhasil disimpan.");
    }

    public function destroy(HomepageSection $section): RedirectResponse
    {
        $this->authorize('delete', $section);

        $title = $section->title;
        $section->delete();
        $this->configService->clearCache();

        return redirect()->route('admin.homepage.index')
            ->with('success', "Bagian '{$title}' berhasil dihapus.");
    }
}
