<?php

namespace App\Observers;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Jobs\ProcessMediaJob;

class MediaObserver
{
  public function created(Media $media): void
    {
        // queue metadata extraction job
        ProcessMediaJob::dispatch($media);
    }

}
