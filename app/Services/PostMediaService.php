<?php

namespace App\Services;

use App\Enums\PostType;
use App\Jobs\ProcessMediaJob;
use App\Models\UserPost;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class UploadResult
{
    public function __construct(
        public readonly bool $hasImage,
        public readonly bool $hasVideo,
        public readonly int  $fileCount,
    ) {}

    public function hasAny(): bool
    {
        return $this->fileCount > 0;
    }
}

class PostMediaService
{
    public function uploadMedia(UserPost $post, array $mediaFiles): UploadResult
    {
        $hasImage = false;
        $hasVideo = false;
        $fileCount = 0;

        foreach ($mediaFiles as $file) {
            if (!($file instanceof UploadedFile)) {
                continue;
            }

            $mimeType = $file->getMimeType();
            $collection = null;

            if (str_starts_with($mimeType, 'image/')) {
                $collection = 'images';
                $hasImage = true;
                $fileCount++;
            } elseif (str_starts_with($mimeType, 'video/')) {
                $collection = 'videos';
                $hasVideo = true;
                $fileCount++;
            }

            if ($collection) {
                $media = $post->addMedia($file)->toMediaCollection($collection);

                if ($media instanceof Media) {
                    try {
                        ProcessMediaJob::dispatch($media);
                    } catch (\Exception $e) {
                        Log::error("Media Processing Job Failed: " . $e->getMessage());
                    }
                }
            } else {
                Log::warning("Skipped file upload: Unsupported MIME type {$mimeType}");
            }
        }

        return new UploadResult($hasImage, $hasVideo, $fileCount);
    }

    public function detectPostType(UploadResult $info, string $requestedType): ?string
    {
        if ($requestedType === PostType::Reel->value) {
            return null;
        }

        $detectedType = null;

        if ($info->hasImage && $info->hasVideo) {
            $detectedType = PostType::Carousel->value;
        } elseif ($info->hasVideo && $info->fileCount > 1) {
            $detectedType = PostType::Carousel->value;
        } elseif ($info->hasImage && $info->fileCount > 1) {
            $detectedType = PostType::Carousel->value;
        } elseif ($info->hasVideo) {
            $detectedType = PostType::Video->value;
        } elseif ($info->hasImage) {
            $detectedType = PostType::Post->value;
        }

        return $detectedType;
    }
}
