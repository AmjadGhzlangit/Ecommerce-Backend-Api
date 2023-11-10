<?php

namespace App\Http\API\V1\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'max:255', 'unique:roles'],
            'description' => ['required', 'max:255'],
        ];
    }
}
