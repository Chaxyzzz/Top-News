<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMediaRequest;
use App\Http\Requests\Admin\UpdateMediaRequest;
use App\Models\Media;
use App\Services\AuditLogService;
use App\Services\MediaService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MediaController extends Controller
{
    public function __construct(
        protected MediaService $mediaService,
        protected AuditLogService $auditLog
    ) {}

    /**
     * Display a paginated listing of media assets in the library.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Media::class);

        $media = $this->mediaService->getMediaList($request, 24);

        return view('admin.media.index', compact('media'));
    }

    /**
     * Store newly uploaded media file(s).
     */
    public function store(StoreMediaRequest $request): JsonResponse|RedirectResponse
    {
        $currentUser = Auth::user();
        $metadata = [
            'alt_text' => $request->input('alt_text'),
            'caption' => $request->input('caption'),
            'credit' => $request->input('credit'),
        ];

        $uploadedMedia = [];

        // 1. Multiple Files Upload
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $media = $this->mediaService->storeUploadedFile($file, $currentUser, $metadata);
                $uploadedMedia[] = $media;
                $this->auditLog->log('media.uploaded', $media, "Media '{$media->original_filename}' diunggah oleh {$currentUser->name}.", $currentUser);
            }
        } elseif ($request->hasFile('file')) {
            // 2. Single File Upload
            $media = $this->mediaService->storeUploadedFile($request->file('file'), $currentUser, $metadata);
            $uploadedMedia[] = $media;
            $this->auditLog->log('media.uploaded', $media, "Media '{$media->original_filename}' diunggah oleh {$currentUser->name}.", $currentUser);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => count($uploadedMedia).' file media berhasil diunggah.',
                'media' => array_map(fn ($m) => [
                    'id' => $m->id,
                    'uuid' => $m->uuid,
                    'url' => $m->url,
                    'thumbnail_url' => $m->thumbnail_url,
                    'medium_url' => $m->medium_url,
                    'large_url' => $m->large_url,
                    'filename' => $m->original_filename,
                    'alt_text' => $m->alt_text,
                    'caption' => $m->caption,
                    'credit' => $m->credit,
                    'dimensions' => $m->dimensions,
                    'size' => $m->human_size,
                ], $uploadedMedia),
            ]);
        }

        return redirect()->route('admin.media.index')
            ->with('success', count($uploadedMedia).' aset media berhasil diunggah ke pustaka.');
    }

    /**
     * Display media detail & metadata view.
     */
    public function show(Media $media): View
    {
        $this->authorize('view', $media);

        $media->load(['uploader', 'articles:id,title,slug,status,published_at', 'galleries:id,title,slug,status']);

        return view('admin.media.show', compact('media'));
    }

    /**
     * Update media metadata (alt text, caption, credit).
     */
    public function update(UpdateMediaRequest $request, Media $media): RedirectResponse
    {
        $this->mediaService->updateMetadata($media, $request->validated());

        $this->auditLog->log('media.updated', $media, "Metadata media '{$media->original_filename}' diperbarui oleh ".Auth::user()->name.'.', Auth::user());

        return redirect()->route('admin.media.show', $media)
            ->with('success', 'Metadata media berhasil diperbarui.');
    }

    /**
     * Remove the specified media asset from storage.
     */
    public function destroy(Media $media): RedirectResponse
    {
        $this->authorize('delete', $media);

        try {
            $filename = $media->original_filename;
            $this->mediaService->deleteMedia($media);

            $this->auditLog->log('media.deleted', null, "Aset media '{$filename}' dihapus oleh ".Auth::user()->name.'.', Auth::user());

            return redirect()->route('admin.media.index')
                ->with('success', "Aset media '{$filename}' berhasil dihapus.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Asynchronous endpoint for modal media picker.
     */
    public function modalList(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Media::class);

        $media = $this->mediaService->getMediaList($request, 18);

        return response()->json([
            'data' => $media->items(),
            'current_page' => $media->currentPage(),
            'last_page' => $media->lastPage(),
            'total' => $media->total(),
        ]);
    }
}
