<?php

namespace App\Http\API\V1\Requests\User\Role;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @bodyParam role_ids int[] List of the roles Ids.
 */
class EditUserRoleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'role_ids' => ['required'],
            'role_ids.*' => ['exists:roles,id'],
        ];
    }
}
