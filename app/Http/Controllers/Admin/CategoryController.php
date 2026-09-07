<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\AuditLogService;
use App\Services\PublicContentCacheService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function __construct(
        protected AuditLogService $auditLog,
        protected PublicContentCacheService $cacheService
    ) {}

    /**
     * Display a listing of categories.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Category::class);

        $categories = Category::with('parent')
            ->withCount('articles')
            ->ordered()
            ->paginate(20);

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create(): View
    {
        $this->authorize('create', Category::class);

        $parentCategories = Category::whereNull('parent_id')->ordered()->get();

        return view('admin.categories.create', compact('parentCategories'));
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['show_on_homepage'] = $request->boolean('show_on_homepage', true);
        $validated['show_in_navigation'] = $request->boolean('show_in_navigation', true);
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);
        $validated['homepage_order'] = (int) ($validated['homepage_order'] ?? 0);
        $validated['navigation_order'] = (int) ($validated['navigation_order'] ?? 0);

        $category = Category::create($validated);
        $this->auditLog->log('category.created', $category, "Kategori '{$category->name}' dibuat oleh ".Auth::user()->name.'.', Auth::user());
        $this->cacheService->invalidateNavigation();

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori '{$category->name}' berhasil dibuat.");
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category): View
    {
        $this->authorize('update', $category);

        $parentCategories = Category::whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->ordered()
            ->get();

        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    /**
     * Update the specified category in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $validated = $request->validated();
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['show_on_homepage'] = $request->boolean('show_on_homepage', true);
        $validated['show_in_navigation'] = $request->boolean('show_in_navigation', true);
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);
        $validated['homepage_order'] = (int) ($validated['homepage_order'] ?? 0);
        $validated['navigation_order'] = (int) ($validated['navigation_order'] ?? 0);

        $category->update($validated);
        $this->auditLog->log('category.updated', $category, "Kategori '{$category->name}' diperbarui oleh ".Auth::user()->name.'.', Auth::user());
        $this->cacheService->invalidateNavigation();

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori '{$category->name}' berhasil diperbarui.");
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Category $category): RedirectResponse
    {
        $this->authorize('delete', $category);

        // Safe delete check: prevent deleting if articles are assigned
        if ($category->articles()->count() > 0) {
            return back()->with('error', "Kategori '{$category->name}' tidak dapat dihapus karena masih digunakan oleh {$category->articles()->count()} artikel. Anda dapat menonaktifkan statusnya.");
        }

        $name = $category->name;
        $category->delete();
        $this->auditLog->log('category.permanently_deleted', $category, "Kategori '{$name}' dihapus permanen oleh ".Auth::user()->name.'.', Auth::user());
        $this->cacheService->invalidateNavigation();

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori '{$name}' berhasil dihapus permanen.");
    }
}
