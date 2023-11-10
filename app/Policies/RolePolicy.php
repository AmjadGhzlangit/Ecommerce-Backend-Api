<?php

namespace App\Policies;

use App\Enums\PermissionType;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class RolePolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function viewAny(User $user): Response
    {
        return $user->checkPermissionTo(PermissionType::INDEX_ROLE->value)
            ? $this->allow()
            : $this->deny(__('auth.permission_required'));
    }

    public function create(User $user): Response
    {
        return $user->checkPermissionTo(PermissionType::STORE_ROLE->value)
            ? $this->allow()
            : $this->deny(__('auth.permission_required'));
    }

    public function view(User $user, Role $model): Response
    {
        return $user->checkPermissionTo(PermissionType::SHOW_ROLE->value)
            ? $this->allow()
            : $this->deny(__('auth.permission_required'));
    }

    public function update(User $user, Role $role): Response
    {
        return $user->checkPermissionTo(PermissionType::UPDATE_ROLE->value)
            ? $this->allow()
            : $this->deny(__('auth.permission_required'));
    }

    public function delete(User $user, Role $role): Response
    {
        return $user->checkPermissionTo(PermissionType::DELETE_ROLE->value)
            ? $this->allow()
            : $this->deny(__('auth.permission_required'));
    }

    public function viewAnyRolePermissions(User $user, Role $role): Response
    {
        return $user->checkPermissionTo(PermissionType::SHOW_ROLE_PERMISSION->value)
            ? $this->allow()
            : $this->deny(__('auth.permission_required'));
    }

    public function createRolePermissions(User $user, Role $role): Response
    {
        return $user->checkPermissionTo(PermissionType::EDIT_ROLE_PERMISSION->value)
            ? $this->allow()
            : $this->deny(__('auth.permission_required'));
    }
}
