<?php

namespace Tests\API\V1\Controllers\User\Role;

use App\Enums\PermissionType;
use App\Models\Role;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\API\V1\V1TestCase;

class IndexTest extends V1TestCase
{
    /**
     * @test
     */
    public function get_roles_to_user_by_user_not_authorized()
    {
        $user = User::factory()->has(Role::factory())->create();
        $response = $this->getJson('admin/users/' . $user->id . '/roles?page=1&per_page=5');
        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                'message' => 'Unauthorized',
                'status_code' => Response::HTTP_UNAUTHORIZED,
            ]);
    }

    /**
     * @test
     */
    public function get_roles_to_user_by_user_not_has_permission()
    {
        $user = User::factory()->has(Role::factory())->create();
        Sanctum::actingAs($user, ['']);
        $response = $this->getJson('admin/users/' . $user->id . '/roles?page=1&per_page=5');
        $response->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertJson([
                'message' => __('auth.permission_required'),
                'status_code' => 403,
            ]);
    }

    /**
     * @test
     */
    public function get_roles_to_user_by_user_has_permission()
    {
        $user = User::factory()->has(Role::factory()->count(15))->create(['id' => 1]);
        $userLogged = $this->getUserHasPermission(PermissionType::SHOW_USER_ROLE);
        Sanctum::actingAs($userLogged, ['']);
        $response = $this->getJson('admin/users/' . $user->id . '/roles?page=2&per_page=5');
        $role = $user->roles->get(6);
        $response->assertStatus(Response::HTTP_OK)
            ->assertJson($this->dataResponsePagination(15, 3));

        $this->saveResponseToFile($response, 'admin/users/roles/index.json');
    }

    /**
     * @test
     */
    public function get_roles_to_user_filter_by_id()
    {
        $user = $this->getUserHasPermission(PermissionType::SHOW_USER_ROLE);
        $user->assignRole(Role::factory()->create(['id' => 19573618]));
        $user->assignRole(Role::factory()->create(['id' => 1957361]));
        $user->assignRole(Role::factory()->create(['id' => 5]));
        Sanctum::actingAs($user, ['']);
        $response = $this->getJson('admin/users/' . $user->id . '/roles?filter[id]=5736');
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(2, 'data');
    }

    /**
     * @test
     */
    public function get_roles_to_user_filter_by_name()
    {
        $user = $this->getUserHasPermission(PermissionType::SHOW_USER_ROLE);
        $user->assignRole(Role::factory()->create(['name' => 'tes']));
        $user->assignRole(Role::factory()->create(['name' => 'test']));
        Sanctum::actingAs($user, ['']);
        $response = $this->getJson('admin/users/' . $user->id . '/roles?filter[name]=tes');
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(2, 'data');
    }

    /**
     * @test
     */
    public function get_roles_to_user_filter_by_description()
    {
        $user = $this->getUserHasPermission(PermissionType::SHOW_USER_ROLE);
        $user->assignRole(Role::factory()->create(['description' => 'tes']));
        $user->assignRole(Role::factory()->create(['description' => 'test']));
        Sanctum::actingAs($user, ['']);
        $response = $this->getJson('admin/users/' . $user->id . '/roles?filter[description]=test');
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(1, 'data');
    }

    /**
     * @test
     */
    public function get_roles_to_user_sort_by_id_ascending()
    {
        $userLoggedIn = $this->getUserHasPermission(PermissionType::SHOW_USER_ROLE);
        $user = User::factory()->has(Role::factory()->count(5))->create();
        Sanctum::actingAs($userLoggedIn, ['']);
        $response = $this->getJson('admin/users/' . $user->id . '/roles?sort=id');
        $response->assertStatus(Response::HTTP_OK);
        $roles = $user->roles()->orderBy('id')->first();
        $content = json_decode($response->getContent());
        $this->assertEquals($roles->id, $content->data[0]->id);
    }

    /**
     * @test
     */
    public function get_roles_to_user_sort_by_id_descending()
    {
        $userLoggedIn = $this->getUserHasPermission(PermissionType::SHOW_USER_ROLE);
        $user = User::factory()->has(Role::factory()->count(5))->create();
        Sanctum::actingAs($userLoggedIn, ['']);
        $response = $this->getJson('admin/users/' . $user->id . '/roles?sort=-id');
        $response->assertStatus(Response::HTTP_OK);
        $roles = $user->roles()->orderByDesc('id')->first();
        $content = json_decode($response->getContent());
        $this->assertEquals($roles->id, $content->data[0]->id);
    }

    /**
     * @test
     */
    public function get_roles_to_user_sort_by_name_ascending()
    {
        $userLoggedIn = $this->getUserHasPermission(PermissionType::SHOW_USER_ROLE);
        $user = User::factory()->has(Role::factory()->count(5))->create();
        Sanctum::actingAs($userLoggedIn, ['']);
        $response = $this->getJson('admin/users/' . $user->id . '/roles?sort=name');
        $response->assertStatus(Response::HTTP_OK);
        $roles = $user->roles()->orderBy('name')->first();
        $content = json_decode($response->getContent());
        $this->assertEquals($roles->name, $content->data[0]->name);
    }

    /**
     * @test
     */
    public function get_roles_to_user_sort_by_name_descending()
    {
        $userLoggedIn = $this->getUserHasPermission(PermissionType::SHOW_USER_ROLE);
        $user = User::factory()->has(Role::factory()->count(5))->create();
        Sanctum::actingAs($userLoggedIn, ['']);
        $response = $this->getJson('admin/users/' . $user->id . '/roles?sort=-name');
        $response->assertStatus(Response::HTTP_OK);
        $roles = $user->roles()->orderByDesc('name')->first();
        $content = json_decode($response->getContent());
        $this->assertEquals($roles->name, $content->data[0]->name);
    }

    /**
     * @test
     */
    public function get_roles_to_user_sort_by_description_ascending()
    {
        $userLoggedIn = $this->getUserHasPermission(PermissionType::SHOW_USER_ROLE);
        $user = User::factory()->has(Role::factory()->count(5))->create();
        Sanctum::actingAs($userLoggedIn, ['']);
        $response = $this->getJson('admin/users/' . $user->id . '/roles?sort=description');
        $response->assertStatus(Response::HTTP_OK);
        $roles = $user->roles()->orderBy('description')->first();
        $content = json_decode($response->getContent());
        $this->assertEquals($roles->description, $content->data[0]->description);
    }

    /**
     * @test
     */
    public function get_roles_to_user_sort_by_description_descending()
    {
        $userLoggedIn = $this->getUserHasPermission(PermissionType::SHOW_USER_ROLE);
        $user = User::factory()->has(Role::factory()->count(5))->create();
        Sanctum::actingAs($userLoggedIn, ['']);
        $response = $this->getJson('admin/users/' . $user->id . '/roles?sort=-description');
        $response->assertStatus(Response::HTTP_OK);
        $roles = $user->roles()->orderByDesc('description')->first();
        $content = json_decode($response->getContent());
        $this->assertEquals($roles->description, $content->data[0]->description);
    }

    private function dataResponsePagination($total, $total_pages)
    {
        return
            ['meta' => ['pagination' => [
                'total' => $total,
                'count' => 5,
                'per_page' => 5,
                'current_page' => 2,
                //                        'total_pages' => $total_pages,
                //                        'links' =>
                //                            [
                //                                'previous' => 'http://localhost/api/v1/users/1/roles?page=1',
                //                                'next' => 'http://localhost/api/v1/users/1/roles?page=3',
                //                            ]
            ],
            ]];
    }
}
