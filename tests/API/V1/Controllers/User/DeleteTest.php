<?php

namespace Tests\API\V1\Controllers\User;

use App\Enums\PermissionType;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\API\V1\V1TestCase;

class DeleteTest extends V1TestCase
{
    /**
     * @test
     */
    public function delete_user_by_id_by_user_not_has_permission()
    {
        $user = User::factory()->create();
        $userLogin = User::factory()->create();
        Sanctum::actingAs($userLogin);
        $response = $this->deleteJson("admin/users/{$user->id}");
        $response->assertStatus(403)
            ->assertJson([
                'message' => __('auth.permission_required'),
                'status_code' => 403,
            ]);
    }

    /**
     * @test
     */
    public function delete_user_by_id_by_user_has_permission()
    {
        $users = User::factory()->create();
        $user = $this->getUserHasPermission(PermissionType::DELETE_USER);
        Sanctum::actingAs($user, ['']);
        $response = $this->deleteJson('admin/users/' . $users->id);
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'The user deleted successfully',
                'status_code' => 200,
            ]);
        $this->assertCount(1, User::all());
        $this->saveResponseToFile($response, 'admin/users/delete.json');
    }

    /**
     * @test
     */
    public function delete_himself_user_by_id_by_user_has_permission()
    {
        $user = $this->getUserHasPermission(PermissionType::DELETE_USER);
        Sanctum::actingAs($user, ['']);
        $response = $this->deleteJson('admin/users/' . $user->id);
        $response->assertStatus(409)
            ->assertJson([
                'message' => 'This operation is not permitted,You can not delete yourself',
                'status_code' => 409,
            ]);
        $this->assertCount(1, User::all());
    }
}
