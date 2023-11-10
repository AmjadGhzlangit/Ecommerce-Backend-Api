<?php

namespace App\Policies;

use App\Enums\PermissionType;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class PermissionPolicy
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
        return $user->checkPermissionTo(PermissionType::SHOW_PERMISSIONS->value)
            ? $this->allow()
            : $this->deny(__('auth.permission_required'));
    }

    public function view(User $user): Response
    {
        return $user->checkPermissionTo(PermissionType::SHOW_PERMISSIONS->value)
            ? $this->allow()
            : $this->deny(__('auth.permission_required'));
    }
}
