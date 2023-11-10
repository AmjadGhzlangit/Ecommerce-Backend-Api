<?php

namespace Tests\API\V1\Controllers\Admin\Role;

use App\Enums\PermissionType;
use App\Models\Role;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\API\V1\V1TestCase;

class StoreTest extends V1TestCase
{
    /**
     * @test
     */
    public function store_role_by_user_not_authorized()
    {
        $response = $this->postJson('admin/roles',
            [
                'name' => 'Name role',
                'description' => 'Description role',
            ]);
        $this->assertCount(0, Role::all());
        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthorized',
                'status_code' => 401,
            ]);
    }

    /**
     * @test
     */
    public function store_role_by_user_has_not_permission_update()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $response = $this->postJson('admin/roles', [
            'name' => 'Name role',
            'description' => 'Description role',
        ]);
        $this->assertCount(0, Role::all());
        $response->assertStatus(403)
            ->assertJson([
                'message' => __('auth.permission_required'),
                'status_code' => 403,
            ]);
    }

    /**
     * @test
     */
    public function store_role_by_user_has_permission_update()
    {
        $user = $this->getUserHasPermission(PermissionType::STORE_ROLE);
        Sanctum::actingAs($user);
        $response = $this->postJson('admin/roles', [
            'name' => 'Name role',
            'description' => 'Description role',
        ]);
        $this->assertCount(1, Role::all());
        $response->assertStatus(200);
        $this->saveResponseToFile($response, 'admin/roles/store.json');
    }

    /**
     * @test
     */
    public function name_is_required()
    {
        $user = $this->getUserHasPermission(PermissionType::STORE_ROLE);
        Sanctum::actingAs($user);

        $response = $this->postJson('admin/roles', [
            'description' => 'Description role',
        ]);
        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'data' => [
                    'name' => ['The name field is required.'],
                ],
                'status_code' => 422,
            ]);
    }

    /**
     * @test
     */
    public function description_is_required()
    {
        $user = $this->getUserHasPermission(PermissionType::STORE_ROLE);
        Sanctum::actingAs($user);
        $response = $this->postJson('admin/roles', [
            'name' => 'Name role',
        ]);
        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'data' => [
                    'description' => ['The description field is required.'],
                ],
                'status_code' => 422,
            ]);
    }

    /**
     * @test
     */
    public function name_is_unique()
    {
        $user = $this->getUserHasPermission(PermissionType::STORE_ROLE);
        $role = Role::factory()->create();
        Sanctum::actingAs($user);
        $response = $this->postJson('admin/roles', [
            'name' => $role->name,
            'description' => 'Description role',
        ]);
        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'data' => [
                    'name' => ['The name has already been taken.'],
                ],
                'status_code' => 422,
            ]);
    }
}
