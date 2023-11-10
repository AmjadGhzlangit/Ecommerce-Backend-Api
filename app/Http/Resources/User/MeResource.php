<?php

namespace App\Http\Resources\User;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * @mixin User
 **/
class MeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        $imageUrl = Storage::disk('public')->url($this->image);

        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'has_verified_email' => $this->hasVerifiedEmail(),
            'phone' => $this->phone,
            'has_verified_phone' => $this->hasVerifiedPhone(),
            'image' => $this->imageUrl(),
        ];
    }
}
