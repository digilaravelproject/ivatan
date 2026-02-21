<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;

class ImageHelper
{
    /**
     * Upload product/service image: resize, compress, convert to webp/jpg, and store in per-user folder.
     *
     * @param UploadedFile $file
     * @param int $userId
     * @param string $type 'cover'|'gallery'|'service'
     * @param int $maxWidth
     * @param int $quality 0-100
     * @param string $diskName Laravel storage disk name (default: 'public')
     * @return string Relative path to stored image
     */
    public static function uploadEcomImage(
        UploadedFile $file,
        int $userId,
        string $type = 'gallery',
        int $maxWidth = 1200,
        int $quality = 80,
        string $diskName = 'public'
    ): string {
        $baseDir = "ecom/{$userId}/{$type}";
        $disk = Storage::disk($diskName);
        // $manager = new ImageManager(); // ✅ new in v3
        $driver = extension_loaded('imagick') ? new ImagickDriver() : new GdDriver();
        $manager = new ImageManager($driver);


        try {
            // Validate it's an image
            if (!Str::startsWith($file->getMimeType(), 'image/')) {
                throw new \Exception('Uploaded file is not a valid image.');
            }

            // Create image instance
            $img = $manager->read($file->getRealPath());

            // Resize if wider than max width
            if ($img->width() > $maxWidth) {
                $img = $img->scaleDown($maxWidth);
            }

            // Try to encode as webp
            try {
                $extension = 'webp';
                $encoded = $img->toWebp($quality)->toString();
            } catch (\Throwable $e) {
                Log::warning('WebP encoding failed, falling back to JPEG.', ['error' => $e->getMessage()]);

                $extension = 'jpg';
                $encoded = $img->toJpeg($quality)->toString();
            }

            // Generate final file path
            $filename = time() . '_' . Str::random(8) . '.' . $extension;
            $relativePath = "{$baseDir}/{$filename}";

            // Store to disk
            $disk->put($relativePath, $encoded);

            return $relativePath;
        } catch (\Throwable $e) {
            Log::error('Image upload failed, storing original.', ['error' => $e->getMessage()]);

            // Fallback: store original file unprocessed
            $extension = $file->getClientOriginalExtension() ?: 'jpg';
            $filename = time() . '_' . Str::random(6) . '.' . $extension;
            $relativePath = "{$baseDir}/{$filename}";

            $disk->put($relativePath, file_get_contents($file->getRealPath()));

            return $relativePath;
        }
    }

    /**
     * Delete image by relative path from the storage disk.
     *
     * @param string|null $relativePath
     * @param string $diskName Laravel storage disk name (default: 'public')
     * @return bool
     */
    public static function deleteEcomImage(?string $relativePath, string $diskName = 'public'): bool
    {
        if (!$relativePath) {
            return false;
        }

        return Storage::disk($diskName)->delete($relativePath);
    }
}
