<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use FFMpeg\FFProbe;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\LaravelImageOptimizer\Facades\ImageOptimizer;
use Illuminate\Support\Facades\Storage;

class ProcessMediaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Media $media;

    /**
     * Create a new job instance.
     */
    public function __construct(Media $media)
    {
        $this->media = $media;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $media = $this->media->fresh();
        if (! $media) {
            return;
        }

        $path = $media->getPath(); // local full path
        $mime = $media->mime_type ?? mime_content_type($path);

        // 🖼️ If image → optimize
        if (str_starts_with($mime, 'image/')) {
            try {
                ImageOptimizer::optimize($path);

                $size = getimagesize($path);
                if ($size) {
                    $media->setCustomProperty('width', $size[0]);
                    $media->setCustomProperty('height', $size[1]);
                }

                $media->setCustomProperty('processed_at', now()->toDateTimeString());
                $media->save();
            } catch (\Throwable $e) {
                \Log::error('Image optimization failed: '.$e->getMessage(), [
                    'media_id' => $media->id,
                ]);
            }
            return;
        }

        // 🎥 If video
        try {
            $ffprobe = FFProbe::create();

            $duration = (int) round($ffprobe->format($path)->get('duration'));
            $streams = $ffprobe->streams($path)->videos();
            $videoStream = $streams->first();
            $width = $videoStream?->get('width');
            $height = $videoStream?->get('height');

            // ✅ Reels: Reject if > 60 sec
            if ($media->model->type === 'reel' && $duration > 60) {
                $media->delete();
                $media->model->delete();
                \Log::warning("Reel rejected (too long)", [
                    'media_id' => $media->id,
                    'duration' => $duration,
                ]);
                return;
            }

            // Save metadata
            $media->setCustomProperty('duration', $duration);
            $media->setCustomProperty('width', $width);
            $media->setCustomProperty('height', $height);
            $media->setCustomProperty('processed_at', now()->toDateTimeString());
            $media->save();

            // Compress video
            $this->compressVideo($path, $media);

        } catch (\Throwable $e) {
            \Log::error('ProcessMediaJob error: '.$e->getMessage(), [
                'media_id' => $media->id,
            ]);
        }
    }

    /**
     * Compress video with FFmpeg (resize + lower bitrate).
     */
    protected function compressVideo(string $path, Media $media): void
    {
        try {
            $compressedPath = storage_path("app/tmp/compressed_{$media->id}.mp4");

            // Run FFmpeg compression
            $cmd = "ffmpeg -i " . escapeshellarg($path) .
                " -vf scale='min(720,iw)':-2 -b:v 1500k -c:v libx264 -preset veryfast -c:a aac -b:a 128k " .
                escapeshellarg($compressedPath) . " -y";

            exec($cmd, $output, $returnCode);

            if ($returnCode === 0 && file_exists($compressedPath)) {
                // Replace old media with compressed
                $media->addMedia($compressedPath)
                    ->withCustomProperties($media->custom_properties ?? [])
                    ->toMediaCollection($media->collection_name);

                // delete temp file
                unlink($compressedPath);

                // delete old original
                Storage::disk($media->disk)->delete($path);

                \Log::info("Video compressed successfully", [
                    'media_id' => $media->id,
                ]);
            } else {
                \Log::error("Video compression failed", [
                    'media_id' => $media->id,
                    'cmd' => $cmd,
                ]);
            }

        } catch (\Throwable $e) {
            \Log::error("Video compression exception: " . $e->getMessage(), [
                'media_id' => $media->id,
            ]);
        }
    }
}
