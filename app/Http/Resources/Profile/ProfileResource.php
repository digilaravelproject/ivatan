<?php

namespace App\Http\Resources\Profile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        if (!$this->resource) {
            return [];
        }

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'type' => $this->type,
            'status' => $this->status,
            'is_active' => (bool) $this->is_active,
            'is_default' => (bool) $this->is_default,
            'approved_at' => $this->approved_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'seller_details' => $this->when($this->relationLoaded('sellerDetails') && $this->sellerDetails, function() {
                return [
                    'id' => $this->sellerDetails->id,
                    'profile_id' => $this->sellerDetails->profile_id,
                    'seller_type' => $this->sellerDetails->seller_type,
                ];
            }),
            'employer_details' => $this->when($this->relationLoaded('employerDetails') && $this->employerDetails, function() {
                return [
                    'id' => $this->employerDetails->id,
                    'profile_id' => $this->employerDetails->profile_id,
                ];
            }),
            'music_details' => $this->when($this->relationLoaded('musicDetails') && $this->musicDetails, function() {
                return [
                    'id' => $this->musicDetails->id,
                    'profile_id' => $this->musicDetails->profile_id,
                ];
            }),
            'creator_details' => $this->when($this->relationLoaded('creatorDetails') && $this->creatorDetails, function() {
                return [
                    'id' => $this->creatorDetails->id,
                    'profile_id' => $this->creatorDetails->profile_id,
                ];
            }),
            'active_subscription' => $this->when($this->relationLoaded('activeSubscription'), $this->activeSubscription),
        ];
    }
}
