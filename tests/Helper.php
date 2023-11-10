<?php

namespace Tests;

use App\Enums\PermissionType;
use App\Enums\RoleType;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

trait Helper
{
    protected function getUserHasPermission($permissionName, array $data = []): Model|Collection|User
    {
        $user = User::factory()->create($data);
        if (!is_array($permissionName)) {
            $permissionName = [$permissionName];
        }
        foreach ($permissionName as $item) {
            $permission = Permission::factory()->create(['name' => $item]);
            $user->givePermissionTo($permission);
        }

        return $user;
    }

    protected function getDashboardUserHasPermission($permissionName = null, $data = [])
    {
        $user = User::factory()->create($data);

        if ($permissionName != null) {
            $permission = Permission::factory()->create(['name' => $permissionName]);
            $user->givePermissionTo($permission);
        }

        $dashboardPermission = Permission::factory()->create(['name' => PermissionType::DASHBOARD_ACCESS]);
        $user->givePermissionTo($dashboardPermission);
        $superAdminRole = Role::factory()->create(['name' => RoleType::SUPER_ADMIN->value]);
        $user->assignRole($superAdminRole);

        return $user;
    }

    protected function saveResponseToFile($response, $filePath, $basePath = 'storage/responses/'): void
    {
        $full_path = base_path($basePath . $filePath);
        File::ensureDirectoryExists(dirname($full_path));
        if (config('app.write_docs_from_tests')) {
            if (File::exists($full_path)) {
                throw new FileException('Response File Already Exist. "FILE IS SAVED BEFORE BY ANOTHER TEST"');
            }
            $content = json_encode($response->json(), JSON_PRETTY_PRINT);
            file_put_contents($full_path, $content);
        }
    }
}

