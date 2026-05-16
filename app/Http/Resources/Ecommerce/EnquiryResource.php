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
            'seller_id' => $this->seller_id,
            'service_id' => $this->service_id,
            'product_id' => $this->product_id,
            'seller' => $this->whenLoaded('seller', fn() => [
                'id' => $this->seller->id,
                'name' => $this->seller->name,
                'email' => $this->seller->email,
            ]),
            'service' => $this->whenLoaded('service'),
            'product' => $this->whenLoaded('product'),
            'subject' => $this->subject,
            'message' => $this->message,
            'status' => $this->status,
            'reply_message' => $this->reply_message,
            'created_at' => $this->created_at,
        ];
    }
}
