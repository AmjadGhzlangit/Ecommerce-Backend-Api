<?php

namespace Tests\API\V1\Controllers\Admin\Role;

use App\Enums\PermissionType;
use App\Models\Role;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\API\V1\V1TestCase;

class ShowTest extends V1TestCase
{
    /**
     * @test
     */
    public function show_role_by_id_by_user_has_permission_show()
    {
        $role = Role::factory()->create();
        $user = $this->getUserHasPermission(PermissionType::SHOW_ROLE);
        Sanctum::actingAs($user);
        $response = $this->get('admin/roles/' . $role->id);
        $response->assertStatus(200);
        $this->saveResponseToFile($response, 'admin/roles/show.json');
    }

    /**
     * @test
     */
    public function show_role_by_id_by_user_not_authorized()
    {
        $role = Role::factory()->create();
        $response = $this->getJson('admin/roles/' . $role->id);
        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthorized', 'status_code' => 401]);
    }

    /**
     * @test
     */
    public function show_role_by_id_by_user_not_has_permission()
    {
        $role = Role::factory()->create();
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $response = $this->getJson('admin/roles/' . $role->id);
        $response->assertStatus(403)
            ->assertJson([
                'message' => __('auth.permission_required'),
                'status_code' => 403,
            ]);
    }

    /**
     * @test
     */
    public function get_role_with_correct_responses()
    {
        $role = Role::factory()->create([
            'id' => 1,
            'name' => 'test',
            'description' => 'This Description For Testing',
            'guard_name' => 'api',
        ]);
        $user = $this->getUserHasPermission(PermissionType::SHOW_ROLE);
        Sanctum::actingAs($user);
        $response = $this->get('admin/roles/' . $role->id);
        $response->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'id' => 1,
                    'name' => 'test',
                    'description' => 'This Description For Testing',
                ],
                'message' => 'success',
                'status_code' => 200,
            ]);
    }
}
