<?php

namespace App\Jobs;

use App\Models\UserStory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\ImageOptimizer\OptimizerChainFactory;

class GenerateThumbnailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $story;

    public function __construct(UserStory $story)
    {
        $this->story = $story;
    }

    public function handle()
    {
        // Get the media object associated with the story
        $media = $this->story->media->first(); // Assuming the first media object is the image

        if (!$media) {
            \Log::error("No media found for story: " . $this->story->id);
            return;
        }

        // Get the image path from the media
        $imagePath = $media->getPath(); // Get the full file path for the image

        // Generate the thumbnail path
        $thumbnailPath = storage_path('app/public/thumbnails/' . $this->story->id . '-thumb.jpg');

        \Log::info("Resizing image: " . $imagePath . " to thumbnail path: " . $thumbnailPath);

        // Use Spatie to resize the image and save the thumbnail
        try {
            $media->getManipulatedImage('thumb', 200, 200)->save($thumbnailPath);
        } catch (\Exception $e) {
            \Log::error("Error resizing image using Spatie: " . $e->getMessage());
            return;
        }

        // Optimize the thumbnail after saving it
        $optimizerChain = OptimizerChainFactory::create();
        try {
            $optimizerChain->optimize($thumbnailPath);  // Optimize the thumbnail
        } catch (\Exception $e) {
            \Log::error("Error optimizing image: " . $e->getMessage());
            return;
        }

        // Update the story with the path to the thumbnail
        $this->story->update([
            'thumbnail_path' => 'thumbnails/' . $this->story->id . '-thumb.jpg'
        ]);
    }
}
