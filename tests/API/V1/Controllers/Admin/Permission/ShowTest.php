<?php

namespace Tests\API\V1\Controllers\Admin\Permission;

use App\Enums\PermissionType;
use App\Models\Permission;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\API\V1\V1TestCase;

class ShowTest extends V1TestCase
{
    /**
     * @test
     */
    public function show_permission_by_id()
    {
        $permission = Permission::factory()->create();
        $user = $this->getUserHasPermission(PermissionType::SHOW_PERMISSIONS);
        Sanctum::actingAs($user, ['']);
        $response = $this->getJson('admin/permissions/' . $permission->id);
        $response->assertStatus(200);

        $this->saveResponseToFile($response, 'admin/permissions/show.json');
    }

    /**
     * @test
     */
    public function show_permission_by_id_by_user_not_authorized()
    {
        $permission = Permission::factory()->create();
        $response = $this->getJson('admin/permissions/' . $permission->id);
        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthorized',
                'status_code' => 401,
            ]);
    }

    /**
     * @test
     */
    public function show_permission_by_id_by_user_has_not_super_admin_role()
    {
        $permission = Permission::factory()->create();
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['']);
        $response = $this->getJson('admin/permissions/' . $permission->id);
        $response->assertStatus(403)
            ->assertJson([
                'message' => __('auth.permission_required'),
                'status_code' => 403,
            ]);
    }

    /**
     * @test
     */
    public function get_permission_with_correct_responses()
    {
        $permission = Permission::factory()->create([
            'id' => 1,
            'name' => 'test',
            'description' => 'This Description Permission For Testing',
        ]);
        $user = $this->getUserHasPermission(PermissionType::SHOW_PERMISSIONS);
        Sanctum::actingAs($user, ['']);
        $response = $this->getJson('admin/permissions/' . $permission->id);
        $response->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'id' => 1,
                    'name' => 'test',
                    'description' => 'This Description Permission For Testing',
                ],
                'message' => 'success', 'status_code' => 200,
            ]);
    }
}
