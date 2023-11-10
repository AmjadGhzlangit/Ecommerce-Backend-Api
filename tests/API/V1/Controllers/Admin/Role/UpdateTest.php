<?php

namespace Tests\API\V1\Controllers\Admin\Role;

use App\Enums\PermissionType;
use App\Models\Role;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\API\V1\V1TestCase;

class UpdateTest extends V1TestCase
{
    /**
     * @test
     */
    public function update_role_by_id_by_user_not_authorized()
    {
        $role = Role::factory()->create();
        $response = $this->putJson('admin/roles/' . $role->id);
        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthorized', 'status_code' => 401]);
    }

    /**
     * @test
     */
    public function update_role_by_id_by_user_not_has_permission_update()
    {
        $role = Role::factory()->create();
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $response = $this->putJson('admin/roles/' . $role->id);
        $response->assertStatus(403)
            ->assertJson(['message' => __('auth.permission_required'), 'status_code' => 403]);
    }

    /**
     * @test
     */
    public function update_role_by_user_has_permission_update()
    {
        $role = Role::factory()->create();
        $user = $this->getUserHasPermission(PermissionType::UPDATE_ROLE);
        Sanctum::actingAs($user);
        $response = $this->putJson('admin/roles/' . $role->id, [
            'name' => 'New Name',
            'description' => 'New description',
        ]);
        $roleUpdated = Role::find($role->id);
        $this->assertSame('New Name', $roleUpdated->name);
        $this->assertSame('New description', $roleUpdated->description);
        $response->assertStatus(200)
            ->assertJson(['message' => 'The role updated successfully', 'status_code' => 200]);
        $this->saveResponseToFile($response, 'admin/roles/update.json');
    }
}
