<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class ShopOrderResource extends JsonResource
{
    public function toArray($request): array
    {
        return [

            'user_id' => $this->user_id,
            'order_line_id' => $this->order_line_id,
            'payment_method' => $this->Payment_method,
            'shipping_address' => $this->Shipping_address,
            'order_total' => $this->order_total,
            'shipping_method' => $this->shipping_method,
            'order_status' => $this->order_status,
        ];
    }
}
