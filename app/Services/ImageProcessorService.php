<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class ImageProcessorService
{
    /**
     * Standard responsive variant target widths in pixels.
     *
     * @var array<string, int>
     */
    protected array $variantWidths = [
        'thumbnail' => 320,
        'medium' => 640,
        'large' => 1024,
        'xlarge' => 1600,
    ];

    /**
     * Process an uploaded image file, correct orientation, and generate optimized WebP/JPEG variants.
     *
     * @param  string  $sourcePath  Absolute path or storage path of the original image
     * @param  string  $directory  Target directory in storage (e.g. 'media/2026/08')
     * @param  string  $disk  Storage disk name
     * @return array{width: int, height: int, variants: array<string, string>}
     */
    public function process(string $sourcePath, string $directory, string $disk = 'public'): array
    {
        $realPath = Storage::disk($disk)->path($sourcePath);

        if (! file_exists($realPath) || ! extension_loaded('gd')) {
            return [
                'width' => 0,
                'height' => 0,
                'variants' => [],
            ];
        }

        $imageInfo = @getimagesize($realPath);
        if (! $imageInfo) {
            return [
                'width' => 0,
                'height' => 0,
                'variants' => [],
            ];
        }

        $origWidth = $imageInfo[0];
        $origHeight = $imageInfo[1];
        $mime = $imageInfo['mime'];

        // Load image resource based on mime type
        $sourceImage = match ($mime) {
            'image/jpeg', 'image/jpg' => @imagecreatefromjpeg($realPath),
            'image/png' => @imagecreatefrompng($realPath),
            'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($realPath) : null,
            'image/gif' => @imagecreatefromgif($realPath),
            default => null,
        };

        if (! $sourceImage) {
            return [
                'width' => $origWidth,
                'height' => $origHeight,
                'variants' => [],
            ];
        }

        // Correct EXIF orientation for JPEGs
        $sourceImage = $this->correctOrientation($sourceImage, $realPath, $mime);
        $curWidth = imagesx($sourceImage);
        $curHeight = imagesy($sourceImage);

        $variants = [];
        $supportsWebp = function_exists('imagewebp');
        $baseName = pathinfo($sourcePath, PATHINFO_FILENAME);

        foreach ($this->variantWidths as $variantName => $targetWidth) {
            // Only generate variant if target width is <= original width or for thumbnail
            if ($targetWidth > $curWidth && $variantName !== 'thumbnail') {
                continue;
            }

            $targetHeight = (int) round(($curHeight / $curWidth) * min($targetWidth, $curWidth));
            $actualWidth = min($targetWidth, $curWidth);

            $targetCanvas = imagecreatetruecolor($actualWidth, $targetHeight);

            // Preserve PNG/WebP alpha transparency
            imagealphablending($targetCanvas, false);
            imagesavealpha($targetCanvas, true);
            $transparent = imagecolorallocatealpha($targetCanvas, 255, 255, 255, 127);
            imagefilledrectangle($targetCanvas, 0, 0, $actualWidth, $targetHeight, $transparent);
            imagealphablending($targetCanvas, true);

            imagecopyresampled(
                $targetCanvas,
                $sourceImage,
                0, 0, 0, 0,
                $actualWidth,
                $targetHeight,
                $curWidth,
                $curHeight
            );

            // Save variant file
            $ext = $supportsWebp ? 'webp' : 'jpg';
            $variantRelativePath = "{$directory}/{$baseName}-{$variantName}.{$ext}";
            $variantFullPath = Storage::disk($disk)->path($variantRelativePath);

            // Ensure directory exists
            $dirPath = dirname($variantFullPath);
            if (! is_dir($dirPath)) {
                @mkdir($dirPath, 0755, true);
            }

            if ($supportsWebp) {
                imagewebp($targetCanvas, $variantFullPath, 88);
            } else {
                imagejpeg($targetCanvas, $variantFullPath, 90);
            }

            imagedestroy($targetCanvas);
            $variants[$variantName] = $variantRelativePath;
        }

        imagedestroy($sourceImage);

        return [
            'width' => $curWidth,
            'height' => $curHeight,
            'variants' => $variants,
        ];
    }

    /**
     * Correct image orientation based on EXIF metadata (for JPEGs).
     *
     * @param  \GdImage  $image
     * @return \GdImage
     */
    protected function correctOrientation($image, string $filePath, string $mime)
    {
        if (! in_array($mime, ['image/jpeg', 'image/jpg']) || ! function_exists('exif_read_data')) {
            return $image;
        }

        $exif = @exif_read_data($filePath);
        if (empty($exif['Orientation'])) {
            return $image;
        }

        return match ($exif['Orientation']) {
            3 => imagerotate($image, 180, 0),
            6 => imagerotate($image, -90, 0),
            8 => imagerotate($image, 90, 0),
            default => $image,
        };
    }
}
