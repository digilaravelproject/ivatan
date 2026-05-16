<?php

namespace App\Http\Resources\History;

use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseHistoryResource extends JsonResource
{
    public function toArray($request): array
    {
        $items = $this->items->map(function ($orderItem) {
            $product = $orderItem->item;
            return [
                'title'    => $product->title ?? '[deleted]',
                'quantity' => $orderItem->quantity,
                'price'    => (float) $orderItem->price,
            ];
        });

        return [
            'order_id'    => $this->id,
            'order_uuid'  => $this->uuid,
            'total_amount' => (float) $this->total_amount,
            'status'      => $this->status,
            'items'       => $items,
            'created_at'  => $this->created_at->toIso8601String(),
        ];
    }
}
