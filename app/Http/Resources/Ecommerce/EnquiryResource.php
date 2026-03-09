<?php

namespace App\Http\Resources\Ecommerce;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EnquiryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'user' => [
                'id' => $this->user_id,
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
            ],
            'service' => $this->whenLoaded('service'),
            'product' => $this->whenLoaded('product'),
            'subject' => $this->subject,
            'message' => $this->message,
            'status' => $this->status,
            'created_at' => $this->created_at,
        ];
    }
}
