<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PageStatus;
use App\Enums\PageType;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePageRequest;
use App\Http\Requests\UpdatePageRequest;
use App\Models\Page;
use App\Services\AuditLogger;
use App\Services\ContentSanitizerService;
use App\Services\SeoService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PageController extends Controller
{
    /**
     * Display a listing of pages.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Page::class);

        $query = Page::query()->with(['creator', 'updater']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $pages = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('admin.pages.index', compact('pages'));
    }

    /**
     * Show form for creating a new page.
     */
    public function create(): View
    {
        $this->authorize('create', Page::class);
        $pageTypes = PageType::cases();

        return view('admin.pages.create', compact('pageTypes'));
    }

    /**
     * Store a newly created page.
     */
    public function store(StorePageRequest $request): RedirectResponse
    {
        $this->authorize('create', Page::class);

        $validated = $request->validated();
        $validated['content'] = ContentSanitizerService::sanitize($validated['content']);
        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        if ($validated['status'] === PageStatus::Published->value) {
            $validated['published_at'] = now();
        }

        $page = Page::create($validated);

        if (class_exists(AuditLogger::class)) {
            AuditLogger::log('page.created', Auth::user(), [
                'page_id' => $page->id,
                'title' => $page->title,
            ]);
        }

        return redirect()->route('admin.pages.index')->with('success', 'Halaman berhasil dibuat.');
    }

    /**
     * Show form for editing the page.
     */
    public function edit(Page $page): View
    {
        $this->authorize('update', $page);
        $pageTypes = PageType::cases();

        return view('admin.pages.edit', compact('page', 'pageTypes'));
    }

    /**
     * Update the page.
     */
    public function update(UpdatePageRequest $request, Page $page): RedirectResponse
    {
        $this->authorize('update', $page);

        $validated = $request->validated();
        $validated['content'] = ContentSanitizerService::sanitize($validated['content']);
        $validated['updated_by'] = Auth::id();

        if ($validated['status'] === PageStatus::Published->value && empty($page->published_at)) {
            $validated['published_at'] = now();
        }

        $page->update($validated);

        if (class_exists(AuditLogger::class)) {
            AuditLogger::log('page.updated', Auth::user(), [
                'page_id' => $page->id,
                'title' => $page->title,
            ]);
        }

        return redirect()->route('admin.pages.index')->with('success', 'Halaman berhasil diperbarui.');
    }

    /**
     * Delete the page. Core protected pages cannot be deleted.
     */
    public function destroy(Page $page): RedirectResponse
    {
        $this->authorize('delete', $page);

        $title = $page->title;
        $page->forceDelete();

        if (class_exists(AuditLogger::class)) {
            AuditLogger::log('page.deleted', Auth::user(), [
                'title' => $title,
            ]);
        }

        return redirect()->route('admin.pages.index')->with('success', "Halaman '{$title}' berhasil dihapus.");
    }

    /**
     * Toggle publication status.
     */
    public function togglePublish(Page $page): RedirectResponse
    {
        $this->authorize('publish', $page);

        $newStatus = $page->status === PageStatus::Published ? PageStatus::Draft : PageStatus::Published;
        $page->update([
            'status' => $newStatus,
            'published_at' => $newStatus === PageStatus::Published ? ($page->published_at ?: now()) : null,
            'updated_by' => Auth::id(),
        ]);

        $msg = $newStatus === PageStatus::Published ? 'Halaman berhasil diterbitkan.' : 'Halaman berhasil dialihkan ke draf.';

        return back()->with('success', $msg);
    }

    /**
     * Preview draft or published page layout.
     */
    public function preview(Page $page): View
    {
        $this->authorize('view', $page);

        $sanitizedContent = ContentSanitizerService::sanitize($page->content);
        $isPreview = true;
        $seoData = app(SeoService::class)->forPage($page);
        $seoData->robots = 'noindex,nofollow';

        return view('pages.page', compact('page', 'sanitizedContent', 'isPreview', 'seoData'));
    }
}
