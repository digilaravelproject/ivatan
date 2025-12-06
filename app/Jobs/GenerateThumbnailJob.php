<?php

namespace App\Jobs;

use App\Models\UserStory;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GenerateThumbnailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public UserStory $story) {}

    public function handle()
    {
        // 1. Only process Video stories
        if ($this->story->type !== 'video') return;

        try {
            $mediaItem = $this->story->getFirstMedia('stories');
            if (!$mediaItem) return;

            // --- STEP 1: DOWNLOAD VIDEO FROM S3 TO LOCAL TEMP ---
            // Hum stream use karenge taaki memory overflow na ho
            $tempVideoPath = tempnam(sys_get_temp_dir(), 'story_video_') . '.mp4';
            $stream = $mediaItem->stream();
            file_put_contents($tempVideoPath, stream_get_contents($stream));
            if (is_resource($stream)) fclose($stream);

            // --- STEP 2: GENERATE THUMBNAIL LOCALLY ---
            $ffmpeg = FFMpeg::create([
                'ffmpeg.binaries'  => config('services.ffmpeg.ffmpeg_path'),
                'ffprobe.binaries' => config('services.ffmpeg.ffprobe_path'),
            ]);

            $video = $ffmpeg->open($tempVideoPath);

            // Thumbnail ke liye local path
            $tempThumbPath = tempnam(sys_get_temp_dir(), 'story_thumb_') . '.jpg';

            // 1 second mark par frame capture karo
            $video->frame(TimeCode::fromSeconds(1))->save($tempThumbPath);

            // --- STEP 3: UPLOAD THUMBNAIL BACK TO S3 (VIA SPATIE) ---
            // Hum isse 'thumbnail' naam ki collection me daal rahe hain
            $this->story->addMedia($tempThumbPath)
                ->toMediaCollection('thumbnail');

            // Note: Spatie apne aap config check karega.
            // Agar config me 's3' set hai, toh ye S3 par upload hoga.

        } catch (\Exception $e) {
            Log::error("S3 Video Thumbnail Error: " . $e->getMessage());
        } finally {
            // --- STEP 4: CLEANUP (IMPORTANT) ---
            // Temp files delete karo warna server bhar jayega
            if (isset($tempVideoPath) && file_exists($tempVideoPath)) @unlink($tempVideoPath);
            if (isset($tempThumbPath) && file_exists($tempThumbPath)) @unlink($tempThumbPath);
        }
    }
}
