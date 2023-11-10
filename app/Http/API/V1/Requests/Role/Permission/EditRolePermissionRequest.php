<?php

namespace App\Http\API\V1\Requests\Role\Permission;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @bodyParam permissionIds int[] List of the permissions Ids.
 */
class EditRolePermissionRequest extends FormRequest
{
    public function rules()
    {
        return [
            'permissionIds' => ['required'],
            'permissionIds.*' => ['exists:permissions,id'],
        ];
    }
}
