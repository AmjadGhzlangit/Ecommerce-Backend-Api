<?php

namespace App\Policies;

use App\Enums\PermissionType;
use App\Models\Category;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class CategoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): Response
    {
        return $user->checkPermissionTo(PermissionType::INDEX_CATEGORY->value)
            ? $this->allow()
            : $this->deny(__('auth.permission_required'));
    }

    public function create(User $user): Response
    {
        return $user->checkPermissionTo(PermissionType::STORE_CATEGORY->value)
            ? $this->allow()
            : $this->deny(__('auth.permission_required'));
    }

    public function view(User $user, Category $category): Response
    {
        return $user->checkPermissionTo(PermissionType::SHOW_CATEGORY->value)
            ? $this->allow()
            : $this->deny(__('auth.permission_required'));
    }

    public function update(User $user, Category $model): Response
    {
        return $user->checkPermissionTo(PermissionType::UPDATE_CATEGORY->value)
            ? $this->allow()
            : $this->deny(__('auth.permission_required'));
    }

    public function delete(User $user, Category $model): Response
    {
        return $user->checkPermissionTo(PermissionType::DELETE_CATEGORY->value)
            ? $this->allow()
            : $this->deny(__('auth.permission_required'));
    }
}
