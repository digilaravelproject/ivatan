<?php

namespace App\Http\Resources\Seller;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SellerFinancialResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $acc = $this->account_number;
        $masked = str_repeat('*', strlen($acc) - 4) . substr($acc, -4);

        return [
            'id' => $this->id,
            'bank_name' => $this->bank_name,
            'account_holder_name' => $this->account_holder_name,
            'account_number_masked' => $masked,
            'ifsc_code' => $this->ifsc_code,
            'account_type' => $this->account_type,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
        ];
    }
}
