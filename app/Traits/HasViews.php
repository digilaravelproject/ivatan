<?php

namespace App\Traits;

use App\Models\View;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Trait HasViews
 * Handles polymorphic view logic.
 */
trait HasViews
{
    /**
     * Get all views for the model.
     */
    public function views(): MorphMany
    {
        return $this->morphMany(View::class, 'viewable');
    }

    /**
     * Get the total view count.
     */
    public function getViewsCountAttribute(): int
    {
        return $this->views()->count();
    }

    /**
     * Check if the current logged-in user has viewed this.
     */
    public function isViewedBy(int $userId): bool
    {
        return $this->views()->where('user_id', $userId)->exists();
    }
}
