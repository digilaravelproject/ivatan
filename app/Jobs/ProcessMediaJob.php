<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\LaravelImageOptimizer\Facades\ImageOptimizer;
use Illuminate\Support\Facades\Log;

class ProcessMediaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Media $media;
    /**
     * 🔴 CRITICAL: Stop infinite loops if FFmpeg fails.
     */
    public $tries = 3;
    // Increase timeout to 10 minutes for large video processing
    public $timeout = 600;

    public function __construct(Media $media)
    {
        $this->media = $media;
    }

    public function handle(): void
    {
        $this->media->refresh(); // Get latest state

        $path = $this->media->getPath();

        if (!file_exists($path)) {
            Log::error("Media file missing at path: {$path}");
            return;
        }

        $mime = $this->media->mime_type;

        // 1. Optimize Images
        if (str_starts_with($mime, 'image/')) {
            try {
                ImageOptimizer::optimize($path);
                clearstatcache();
                $this->media->size = filesize($path);
                $this->media->save();
            } catch (\Exception $e) {
                Log::error("Image optimization failed: " . $e->getMessage());
            }
            return;
        }

        // 2. Process Videos (Compress & Fast Start)
        if (str_starts_with($mime, 'video/')) {
            $this->processVideo($path);
        }
    }

    protected function processVideo(string $path): void
    {
        try {
            Log::info("Starting video processing for Media ID: {$this->media->id}");

            // A. Get Metadata using FFprobe
            // Note: Ensure ffmpeg is installed on your server/local machine
            $probeCmd = "ffprobe -v error -select_streams v:0 -show_entries stream=width,height,duration -of default=noprint_wrappers=1:nokey=1 " . escapeshellarg($path);
            exec($probeCmd, $output, $res);

            if ($res === 0 && count($output) >= 3) {
                $width = (int) $output[0];
                $height = (int) $output[1];
                $duration = (int) $output[2];

                // REEL CHECK: Delete if > 65 seconds
                if ($this->media->model->type === 'reel' && $duration > 65) {
                    Log::warning("Reel rejected (Too long: {$duration}s)");
                    $this->media->delete();
                    return; // Stop processing
                }

                // Save Metadata
                $this->media->setCustomProperty('width', $width);
                $this->media->setCustomProperty('height', $height);
                $this->media->setCustomProperty('duration', $duration);
                $this->media->save();
            } else {
                Log::error("FFprobe failed. Is FFmpeg installed and in PATH? Output: " . implode(" ", $output));
                return;
            }
            // B. Compress & Add Fast Start Flag
            $tempPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'compressed_' . $this->media->id . '.mp4';

            // Command Logic:
            // -crf 28: Good balance of quality vs size for mobile (lower is better quality)
            // -preset fast: Speed of compression
            // -movflags +faststart: CRITICAL for instant streaming
            $cmd = "ffmpeg -i " . escapeshellarg($path) . " -vcodec libx264 -crf 28 -preset fast -movflags +faststart -acodec aac -b:a 128k " . escapeshellarg($tempPath) . " -y";

            exec($cmd, $compressOutput, $compressRes);

            if ($compressRes === 0 && file_exists($tempPath)) {
                // Overwrite original file with compressed version
                if (rename($tempPath, $path)) {
                    clearstatcache();
                    $this->media->size = filesize($path);
                    $this->media->setCustomProperty('is_compressed', true);
                    $this->media->setCustomProperty('processed_at', now()->toDateTimeString());
                    $this->media->save();

                    Log::info("Video compressed & optimized successfully.");
                }
            } else {
                Log::error("FFmpeg compression failed.");
            }
        } catch (\Exception $e) {
            Log::error("Video processing exception: " . $e->getMessage());
        }
    }
}
