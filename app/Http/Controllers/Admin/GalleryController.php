<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreGalleryRequest;
use App\Http\Requests\Admin\UpdateGalleryRequest;
use App\Models\Gallery;
use App\Models\Media;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GalleryController extends Controller
{
    public function __construct(
        protected AuditLogService $auditLog
    ) {}

    /**
     * Display a listing of galleries.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Gallery::class);

        $query = Gallery::with(['coverMedia', 'author', 'photographer'])
            ->withCount('media')
            ->latest('created_at');

        if ($search = $request->query('search')) {
            $term = '%'.trim($search).'%';
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', $term)
                    ->orWhere('description', 'like', $term)
                    ->orWhere('photographer_name', 'like', $term);
            });
        }

        $galleries = $query->paginate(20)->withQueryString();

        return view('admin.galleries.index', compact('galleries'));
    }

    /**
     * Show the form for creating a new gallery.
     */
    public function create(): View
    {
        $this->authorize('create', Gallery::class);

        $photographers = User::whereHas('roles', fn ($q) => $q->whereIn('name', ['journalist', 'editor', 'editor_in_chief', 'contributor', 'admin', 'super_admin']))
            ->orderBy('name')
            ->get();

        return view('admin.galleries.create', compact('photographers'));
    }

    /**
     * Store a newly created gallery in storage.
     */
    public function store(StoreGalleryRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $currentUser = Auth::user();

        $gallery = DB::transaction(function () use ($validated, $currentUser) {
            $gallery = Gallery::create([
                'title' => $validated['title'],
                'slug' => ! empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['title']),
                'description' => $validated['description'] ?? null,
                'cover_media_id' => $validated['cover_media_id'] ?? null,
                'author_id' => $currentUser->id,
                'photographer_name' => $validated['photographer_name'] ?? null,
                'photographer_id' => $validated['photographer_id'] ?? null,
                'status' => $validated['status'],
                'published_at' => $validated['status'] === 'published' ? now() : null,
            ]);

            // Sync media attachments
            if (! empty($validated['media_ids'])) {
                $syncData = [];
                foreach ($validated['media_ids'] as $idx => $mediaId) {
                    $syncData[$mediaId] = [
                        'sort_order' => $idx + 1,
                        'caption_override' => $validated['captions'][$mediaId] ?? null,
                        'credit_override' => $validated['credits'][$mediaId] ?? null,
                    ];
                }
                $gallery->media()->sync($syncData);
            }

            $this->auditLog->log('gallery.created', $gallery, "Galeri foto '{$gallery->title}' dibuat oleh {$currentUser->name}.", $currentUser);

            return $gallery;
        });

        return redirect()->route('admin.galleries.edit', $gallery)
            ->with('success', "Galeri '{$gallery->title}' berhasil dibuat.");
    }

    /**
     * Show the form for editing the specified gallery.
     */
    public function edit(Gallery $gallery): View
    {
        $this->authorize('update', $gallery);

        $gallery->load(['media', 'coverMedia', 'photographer']);

        $photographers = User::whereHas('roles', fn ($q) => $q->whereIn('name', ['journalist', 'editor', 'editor_in_chief', 'contributor', 'admin', 'super_admin']))
            ->orderBy('name')
            ->get();

        return view('admin.galleries.edit', compact('gallery', 'photographers'));
    }

    /**
     * Update the specified gallery in storage.
     */
    public function update(UpdateGalleryRequest $request, Gallery $gallery): RedirectResponse
    {
        $validated = $request->validated();
        $currentUser = Auth::user();

        DB::transaction(function () use ($gallery, $validated, $currentUser) {
            $gallery->update([
                'title' => $validated['title'],
                'slug' => ! empty($validated['slug']) ? Str::slug($validated['slug']) : $gallery->slug,
                'description' => $validated['description'] ?? null,
                'cover_media_id' => $validated['cover_media_id'] ?? null,
                'photographer_name' => $validated['photographer_name'] ?? null,
                'photographer_id' => $validated['photographer_id'] ?? null,
                'status' => $validated['status'],
                'published_at' => $validated['status'] === 'published' ? ($gallery->published_at ?? now()) : null,
            ]);

            // Sync media attachments with custom captions & sort orders
            $syncData = [];
            if (! empty($validated['media_ids'])) {
                foreach ($validated['media_ids'] as $idx => $mediaId) {
                    $syncData[$mediaId] = [
                        'sort_order' => $idx + 1,
                        'caption_override' => $validated['captions'][$mediaId] ?? null,
                        'credit_override' => $validated['credits'][$mediaId] ?? null,
                    ];
                }
            }
            $gallery->media()->sync($syncData);

            $this->auditLog->log('gallery.updated', $gallery, "Galeri foto '{$gallery->title}' diperbarui oleh {$currentUser->name}.", $currentUser);
        });

        return redirect()->route('admin.galleries.edit', $gallery)
            ->with('success', "Galeri '{$gallery->title}' berhasil diperbarui.");
    }

    /**
     * Remove the specified gallery from storage.
     */
    public function destroy(Gallery $gallery): RedirectResponse
    {
        $this->authorize('delete', $gallery);

        DB::transaction(function () use ($gallery, $currentUser, $title) {
            $gallery->media()->detach();
            if ($gallery->photoStory) {
                $gallery->photoStory()->delete();
            }
            $gallery->forceDelete();
            $this->auditLog->log('gallery.permanently_deleted', null, "Galeri foto '{$title}' dihapus permanen oleh {$currentUser->name}.", $currentUser);
        });

        return redirect()->route('admin.galleries.index')
            ->with('success', "Galeri '{$title}' berhasil dihapus permanen.");
    }

    /**
     * Asynchronous endpoint to reorder photos in a gallery.
     */
    public function reorder(Request $request, Gallery $gallery): JsonResponse
    {
        $this->authorize('update', $gallery);

        $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['exists:media,id'],
        ]);

        foreach ($request->input('order') as $index => $mediaId) {
            $gallery->media()->updateExistingPivot($mediaId, [
                'sort_order' => $index + 1,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Urutan foto berhasil disimpan.']);
    }
}
