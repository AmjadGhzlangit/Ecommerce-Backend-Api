<?php

namespace Tests\API\V1\Controllers\Admin\Role;

use App\Enums\PermissionType;
use App\Models\Role;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\API\V1\V1TestCase;

class DeleteTest extends V1TestCase
{
    /**
     * @test
     */
    public function delete_role_by_id_by_user_not_authorized()
    {
        $role = Role::factory()->create();
        $response = $this->deleteJson('admin/roles/' . $role->id);
        $this->assertCount($role->count(), Role::all());
        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthorized',
                'status_code' => 401,
            ]);
    }

    /**
     * @test
     */
    public function delete_role_by_id_by_user_not_has_permission_delete()
    {
        $role = Role::factory()->create();
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $response = $this->deleteJson('admin/roles/' . $role->id);
        $this->assertCount($role->count(), Role::all());
        $response->assertStatus(403)
            ->assertJson([
                'message' => __('auth.permission_required'),
                'status_code' => 403,
            ]);
    }

    /**
     * @test
     */
    public function delete_role_by_id_by_user_has_permission_delete()
    {
        $role = Role::factory()->create();
        $user = $this->getUserHasPermission(PermissionType::DELETE_ROLE);
        Sanctum::actingAs($user);

        $response = $this->deleteJson('admin/roles/' . $role->id);
        $this->assertCount(0, Role::all());
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'The role deleted successfully',
                'status_code' => 200,
            ]);
        $this->saveResponseToFile($response, 'admin/roles/delete.json');
    }

    /**
     * @test
     */
    public function delete_role_by_id_by_user_super_admin()
    {
        $role = Role::factory()->create();
        $user = $this->getUserHasPermission(PermissionType::DELETE_ROLE);
        Sanctum::actingAs($user);
        $response = $this->deleteJson('admin/roles/' . $role->id);
        $this->assertCount(0, Role::all());
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'The role deleted successfully',
                'status_code' => 200,
            ]);
    }
}
