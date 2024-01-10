<?php

namespace App\Policies;
use App\Enums\PermissionType;
use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class ProductPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): Response
    {
        return $user->CheckPermissionTo(PermissionType::INDEX_PRODUCT->value)
        ? $this->allow()
        : $this->deny(__('auth.permission_required'));
    }

    public function create(User $user): Response
    {
        return $user->CheckPermissionTo(PermissionType::STORE_PRODUCT->value)
        ? $this->allow()
        : $this->deny(__('auth.permission_required'));

    }

    public function view(User $user, Product $product): Response
    {
        return $user->checkPermissionTo(PermissionType::SHOW_PRODUCT->value)
        ? $this->allow()
        : $this->deny(__('auth.permission_required'));
    }

    public function update(User $user, Product $model): Response
    {
        return $user->checkPermissionTo(PermissionType::UPDATE_PRODUCT->value)
        ? $this->allow()
        : $this->deny(__('auth.permission_required'));

    }

    public function delete(User $user, Product $model): Response
    {
        return $user->checkPermissionTo(PermissionType::DELETE_PRODUCT->value)
        ? $this->allow()
        : $this->deny(__('auth.permission_required'));
    }
}