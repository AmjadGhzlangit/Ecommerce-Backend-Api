<?php

namespace Tests\API\V1\Controllers\User\Role;

use App\Enums\PermissionType;
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
    public function edit_roles_to_user_by_user_not_has_permission()
    {
        $user = User::factory()->has(Role::factory()->count(1))->create();
        $roles = Role::factory()->count(3)->create();
        Sanctum::actingAs($user, ['']);
        $response = $this->postJson('admin/users/' . $user->id . '/roles', [
            'role_ids' => $roles->pluck('id'),
        ]);
        $this->assertCount(1, $user->roles);
        $response->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertJson([
                'message' => __('auth.permission_required'),
                'status_code' => 403,
            ]);
    }

    /**
     * @test
     */
    public function edit_roles_to_user_by_user_has_permission_with_roles_id_empty()
    {
        $user = User::factory()->create();
        Role::factory()->count(3)->create();
        $userLogged = $this->getUserHasPermission(PermissionType::EDIT_USER_ROLE);
        Sanctum::actingAs($userLogged, ['']);
        $response = $this->postJson('admin/users/' . $user->id . '/roles');
        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'data' => [
                    'role_ids' => ['The role ids field is required.'],
                ],
                'status_code' => 422,
            ]);

    }

    /**
     * @test
     */
    public function edit_roles_to_user_by_user_has_permission()
    {
        $user = User::factory()->create();
        $roles = Role::factory()->count(3)->create();
        $userLogged = $this->getUserHasPermission(PermissionType::EDIT_USER_ROLE);
        Sanctum::actingAs($userLogged, ['']);
        $response = $this->postJson('admin/users/' . $user->id . '/roles', [
            'role_ids' => $roles->pluck('id'),
        ]);
        $this->assertCount(3, $user->roles);
        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => "The user's roles updated successfully.",
                'status_code' => Response::HTTP_OK,
            ]);

        $this->saveResponseToFile($response, 'admin/users/roles/store.json');
    }
}
