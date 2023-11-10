<?php

namespace App\Http\Resources\Auth\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserOrderResource extends JsonResource
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
            'products' => $this->products->map(function ($product) {
                $priceDescription = $product->price + ($product->price * ($product->vatPercentage / 100));

                return [
                    'id' => $product->id,
                    'quantity' => $product->pivot->quantity,
                    'image' => $product->image,
                    'imageDescription' => $product->name,
                    'price' => $product->price,
                    'vatPercentage' => $product->vatPercentage,
                    'finalPrice' => $priceDescription,
                ];
            }),
            'total_amount' => $this->total_amount,
            'final_amount' => $this->final_amount,
            'total_quantity' => $this->total_quantity,
            'status' => [
                'key' => $this->order_status,
                'value' => __($this->order_status->name),
            ],
            'tracking_number' => $this->tracking_number,
            'payment_method' => $this->payment_method,
            'notes' => $this->additional_notes,
        ];
    }
}
