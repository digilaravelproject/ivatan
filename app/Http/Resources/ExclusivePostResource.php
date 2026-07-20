<?php

namespace App\Http\Resources;

use Illuminate\Support\Facades\Auth;

class ExclusivePostResource extends PostResource
{
    public function toArray($request): array
    {
        $data = parent::toArray($request);

        /** @var \App\Models\User|null $authUser */
        $authUser = Auth::guard('sanctum')->user() ?? Auth::user();
        
        $isMine = false;
        if ($authUser && $this->user) {
            $isMine = $authUser->id === $this->user->id;
        }

        $hasAccess = true;
        if ($this->is_exclusive) {
            if (!$authUser) {
                $hasAccess = false;
            } elseif ($isMine) {
                $hasAccess = true;
            } elseif ($authUser->is_admin) {
                $hasAccess = true;
            } else {
                $hasAccess = $authUser->hasExclusiveAccessTo($this->id);
            }
        }

        // Add exclusive specific fields
        $data['is_exclusive'] = (bool)$this->is_exclusive;
        $data['price'] = $this->is_exclusive ? (float)$this->price : null;
        $data['exclusive_status'] = $this->exclusive_status;
        $data['has_access'] = $hasAccess;

        $data['media'] = $this->media->map(function ($m) use ($hasAccess) {
            return [
                'id' => $m->id,
                'type' => str_starts_with($m->mime_type, 'video') ? 'video' : 'image',
                'url' => $hasAccess ? $m->getUrl() : null,
                'thumbnail' => $hasAccess ? ($m->hasGeneratedConversion('thumb') ? $m->getUrl('thumb') : $m->getUrl()) : null,
                'mime_type' => $m->mime_type,
                'aspect_ratio' => $m->getCustomProperty('aspect_ratio', null),
            ];
        });

        return $data;
    }
}
