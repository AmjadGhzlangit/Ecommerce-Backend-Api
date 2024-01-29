<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'name' => $this->name,
            'description' => $this->description,
           'image' => $this->imageUrl(),
           'sku' => $this->sku,
            'qty' => $this->qty,
            'price' => number_format($this->price, 2),
             'slug' => $this->slug,
             'currency' => $this->currency,
                    ];
    }
}
