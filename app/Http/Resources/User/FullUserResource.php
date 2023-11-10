<?php

namespace App\Http\Resources\User;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 **/
class FullUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'country_id' => $this->country_id,
            'image' => $this->imageUrl(),
            'has_verified_email' => $this->hasVerifiedEmail(),
            'has_verified_phone' => $this->hasVerifiedPhone(),


        ];
    }
}
