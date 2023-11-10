<?php

namespace Tests\API\V1\Controllers\Admin\Role\Permission;

use App\Enums\PermissionType;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\API\V1\V1TestCase;

class StoreTest extends V1TestCase
{
    /**
     * @test
     */
    public function edit_permissions_to_role_by_user_not_authorized()
    {
        $role = Role::factory()->has(Permission::factory()->count(1))->create();
        $response = $this->postJson('admin/roles/' . $role->id . '/permissions');
        $this->assertCount(1, $role->permissions);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                'message' => 'Unauthorized',
                'status_code' => Response::HTTP_UNAUTHORIZED,
            ]);
    }

    /**
     * @test
     */
    public function edit_permissions_to_role_by_user_not_has_permission()
    {
        Permission::factory()->create([
            'id' => 1,
        ]);
        $role = Role::factory()->has(Permission::factory()->count(1))->create();
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['']);
        $response = $this->postJson('admin/roles/' . $role->id . '/permissions', ['permissionIds' => [1]]);
        $this->assertCount(1, $role->permissions);
        $response->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertJson([
                'message' => __('auth.permission_required'),
                'status_code' => Response::HTTP_FORBIDDEN,
            ]);
    }

    /**
     * @test
     */
    public function edit_permissions_to_role_by_user_has_permission()
    {
        $permissions = Permission::factory()->count(2)->create();
        $role = Role::factory()->has(Permission::factory()->count(1))->create();
        $user = $this->getUserHasPermission(PermissionType::EDIT_ROLE_PERMISSION);
        Sanctum::actingAs($user, ['']);
        $response = $this->postJson('admin/roles/' . $role->id . '/permissions', [
            'permissionIds' => $permissions->pluck('id'),
        ]);
        $this->assertCount(2, $role->permissions);
        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => "The role's permissions updated successfully.",
                'status_code' => Response::HTTP_OK,
            ]);
        $this->saveResponseToFile($response, 'admin/roles/permissions/store.json');
    }

    /**
     * @test
     */
    public function edit_permissions_to_role_by_user_super_admin()
    {
        $permissions = Permission::factory()->count(2)->create();
        $role = Role::factory()->has(Permission::factory()->count(1))->create();
        $user = $this->getUserHasPermission(PermissionType::EDIT_ROLE_PERMISSION);
        Sanctum::actingAs($user, ['']);
        $response = $this->postJson('admin/roles/' . $role->id . '/permissions', [
            'permissionIds' => $permissions->pluck('id')->toArray(),
        ]);
        $this->assertCount(2, $role->permissions);
        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => "The role's permissions updated successfully.",
                'status_code' => Response::HTTP_OK,
            ]);
    }
}
