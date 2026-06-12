<?php

namespace App\Http\Resources\Ecommerce;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductImageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'url' => $this->image_url ?? $this->url ?? $this->path,
            'sort_order' => $this->sort_order ?? 0,
        ];
    }
}
