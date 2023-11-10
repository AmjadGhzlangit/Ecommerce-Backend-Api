<?php

namespace App\Http\Resources\Auth;

use App\Http\Resources\User\MeResource;
use Illuminate\Http\Resources\Json\JsonResource;

class LoginResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'token_type' => $this['token_type'],
            'access_token' => $this['access_token'],
            'access_expires_at' => $this['access_expires_at'],
            'profile' => new MeResource($this['user']),
        ];
    }
}
