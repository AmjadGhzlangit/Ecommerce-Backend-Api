<?php

namespace App\Http\Resources\Permission;

use App\Models\Permission;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Permission
 **/
class PermissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
        ];
    }
}
