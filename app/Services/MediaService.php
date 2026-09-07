<?php

namespace App\Services;

use App\Enums\MediaType;
use App\Models\Media;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaService
{
    public function __construct(
        protected ImageProcessorService $imageProcessor
    ) {}

    /**
     * Store an uploaded file, process variants, and create a Media record.
     *
     * @param  array{alt_text?: ?string, caption?: ?string, credit?: ?string}  $metadata
     */
    public function storeUploadedFile(UploadedFile $file, ?User $user = null, array $metadata = [], string $disk = 'public'): Media
    {
        $uuid = (string) Str::uuid();
        $datePath = date('Y/m');
        $directory = "media/{$datePath}";
        $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $originalFilename = $file->getClientOriginalName();
        $mimeType = $file->getClientMimeType();
        $size = $file->getSize();
        $filename = "{$uuid}.{$extension}";
        $storagePath = "{$directory}/{$filename}";

        // 1. Store original file to disk
        $path = $file->storeAs($directory, $filename, $disk);

        // 2. Process image variants and dimensions if it's an image
        $width = null;
        $height = null;
        $variants = [];
        $mediaType = MediaType::Image;

        if (str_starts_with($mimeType, 'image/')) {
            $processed = $this->imageProcessor->process($storagePath, $directory, $disk);
            $width = $processed['width'] ?: null;
            $height = $processed['height'] ?: null;
            $variants = $processed['variants'];
        } elseif (str_starts_with($mimeType, 'video/')) {
            $mediaType = MediaType::Video;
        } else {
            $mediaType = MediaType::Document;
        }

        // 3. Create Media database record
        return Media::create([
            'uuid' => $uuid,
            'disk' => $disk,
            'path' => $path,
            'filename' => $filename,
            'original_filename' => $originalFilename,
            'mime_type' => $mimeType,
            'extension' => $extension,
            'size' => $size,
            'width' => $width,
            'height' => $height,
            'alt_text' => $metadata['alt_text'] ?? null,
            'caption' => $metadata['caption'] ?? null,
            'credit' => $metadata['credit'] ?? null,
            'media_type' => $mediaType,
            'variants' => $variants,
            'uploaded_by' => $user?->id,
        ]);
    }

    /**
     * Get paginated media list with search and filters.
     */
    public function getMediaList(Request $request, int $perPage = 24): LengthAwarePaginator
    {
        $query = Media::with('uploader:id,name,username')
            ->withCount(['articles', 'galleries'])
            ->latest('created_at');

        if ($search = $request->query('search')) {
            $query->search($search);
        }

        if ($type = $request->query('type')) {
            if ($typeEnum = MediaType::tryFrom($type)) {
                $query->where('media_type', $typeEnum);
            }
        }

        if ($uploaderId = $request->query('uploader_id')) {
            $query->where('uploaded_by', $uploaderId);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Update media metadata (alt text, caption, credit).
     *
     * @param  array{alt_text?: ?string, caption?: ?string, credit?: ?string}  $data
     */
    public function updateMetadata(Media $media, array $data): Media
    {
        $media->update([
            'alt_text' => $data['alt_text'] ?? $media->alt_text,
            'caption' => $data['caption'] ?? $media->caption,
            'credit' => $data['credit'] ?? $media->credit,
        ]);

        return $media;
    }

    /**
     * Delete media record and associated physical files.
     *
     * @throws \Exception If media is currently in use
     */
    public function deleteMedia(Media $media): bool
    {
        if ($media->isUsed()) {
            throw new \Exception("Media '{$media->original_filename}' sedang digunakan dalam artikel atau galeri dan tidak dapat dihapus.");
        }

        return DB::transaction(function () use ($media) {
            $disk = $media->disk ?: 'public';

            // Delete generated variants
            if (! empty($media->variants) && is_array($media->variants)) {
                foreach ($media->variants as $variantPath) {
                    if (! empty($variantPath) && Storage::disk($disk)->exists($variantPath)) {
                        Storage::disk($disk)->delete($variantPath);
                    }
                }
            }

            // Delete original file
            if (! empty($media->path) && Storage::disk($disk)->exists($media->path)) {
                Storage::disk($disk)->delete($media->path);
            }

            return (bool) $media->forceDelete();
        });
    }
}
