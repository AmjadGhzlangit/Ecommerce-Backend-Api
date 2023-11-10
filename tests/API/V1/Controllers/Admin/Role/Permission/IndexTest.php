<?php

namespace Tests\API\V1\Controllers\Admin\Role\Permission;

use App\Enums\PermissionType;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\API\V1\V1TestCase;

class IndexTest extends V1TestCase
{
    //TODO: complete this
    /**
     * @test
     */
    public function get_permissions_to_role_by_user_not_authorized()
    {
        $role = Role::factory()->create();
        $response = $this->getJson('admin/roles/' . $role->id . '/permissions?page=1&per_page=5');
        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson(['message' => 'Unauthorized', 'status_code' => Response::HTTP_UNAUTHORIZED]);
    }

    /**
     * @test
     */
    public function get_permissions_to_role_by_user_not_has_permission()
    {
        $user = User::factory()->create();
        $role = Role::factory()->create();
        Sanctum::actingAs($user, ['']);
        $response = $this->getJson('admin/roles/' . $role->id . '/permissions?page=1&per_page=5');
        $response->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertJson(['message' => __('auth.permission_required'), 'status_code' => Response::HTTP_FORBIDDEN]);
    }

    /**
     * @test
     */
    public function get_permissions_to_role_by_user_has_permission()
    {
        $user = $this->getUserHasPermission(PermissionType::SHOW_ROLE_PERMISSION);
        $role = Role::factory()->has(Permission::factory()->count(15))->create(['id' => 1]);
        Sanctum::actingAs($user, ['']);
        $response = $this->getJson('admin/roles/' . $role->id . '/permissions?page=2&per_page=5');
        $permission = $role->permissions->get(6);
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(5, 'data')
            ->assertJson($this->dataResponsePagination(15, 3));

        $this->saveResponseToFile($response, 'admin/roles/permissions/index.json');
    }

    /**
     * @test
     */
    public function get_permissions_to_role_filter_by_id()
    {
        $user = $this->getUserHasPermission(PermissionType::SHOW_ROLE_PERMISSION);
        $role = Role::factory()->create();
        Permission::factory()->create([
            'id' => 11,
        ])->assignRole($role);
        Permission::factory()->create([
            'id' => 111,
        ])->assignRole($role);
        Permission::factory()->create([
            'id' => 5,
        ])->assignRole($role);
        Sanctum::actingAs($user, ['']);
        $response = $this->getJson('admin/roles/' . $role->id . '/permissions?filter[id]=1');
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(2, 'data');
    }

    /**
     * @test
     */
    public function get_permissions_to_role_filter_by_name()
    {
        $user = $this->getUserHasPermission(PermissionType::SHOW_ROLE_PERMISSION);
        $role = Role::factory()->create();
        Permission::factory()->create([
            'name' => 'test1',
        ])->assignRole($role);
        Permission::factory()->create([
            'name' => 'test',
        ])->assignRole($role);
        Permission::factory()->create([
        ])->assignRole($role);
        Sanctum::actingAs($user, ['']);
        $response = $this->getJson('admin/roles/' . $role->id . '/permissions?filter[name]=test');
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(2, 'data');
    }

    /**
     * @test
     */
    public function get_permissions_to_role_filter_by_description()
    {
        $user = $this->getUserHasPermission(PermissionType::SHOW_ROLE_PERMISSION);
        $role = Role::factory()->create();
        Permission::factory()->create([
            'description' => 'test',
        ])->assignRole($role);
        Permission::factory()->create([
            'description' => 'test',
        ])->assignRole($role);
        Permission::factory()->create([
        ])->assignRole($role);
        Sanctum::actingAs($user, ['']);
        $response = $this->getJson('admin/roles/' . $role->id . '/permissions?filter[description]=test');
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(2, 'data');
    }

    /**
     * @test
     */
    public function get_permissions_to_role_sort_by_id_ascending()
    {
        $role = Role::factory()->has(Permission::factory()->count(5))->create();
        $user = $this->getUserHasPermission(PermissionType::SHOW_ROLE_PERMISSION);
        Sanctum::actingAs($user, ['']);
        $response = $this->getJson('admin/roles/' . $role->id . '/permissions?sort=id');
        $response->assertStatus(Response::HTTP_OK);
        $permissions = $role->permissions()->orderBy('id')->first();
        $content = json_decode($response->getContent());
        $this->assertEquals($permissions->id, $content->data[0]->id);
    }

    /**
     * @test
     */
    public function get_permissions_to_role_sort_by_id_descending()
    {
        $role = Role::factory()->has(Permission::factory()->count(5))->create();
        $user = $this->getUserHasPermission(PermissionType::SHOW_ROLE_PERMISSION);
        Sanctum::actingAs($user, ['']);
        $response = $this->getJson('admin/roles/' . $role->id . '/permissions?sort=-id');
        $response->assertStatus(Response::HTTP_OK);
        $permissions = $role->permissions()->orderByDesc('id')->first();
        $content = json_decode($response->getContent());
        $this->assertEquals($permissions->id, $content->data[0]->id);
    }

    /**
     * @test
     */
    public function get_permissions_to_role_sort_by_name_ascending()
    {
        $role = Role::factory()->has(Permission::factory()->count(5))->create();
        $user = $this->getUserHasPermission(PermissionType::SHOW_ROLE_PERMISSION);
        Sanctum::actingAs($user, ['']);
        $response = $this->getJson('admin/roles/' . $role->id . '/permissions?sort=name');
        $response->assertStatus(Response::HTTP_OK);
        $permissions = $role->permissions()->orderBy('name')->first();
        $content = json_decode($response->getContent());
        $this->assertEquals($permissions->name, $content->data[0]->name);
    }

    /**
     * @test
     */
    public function get_permissions_to_role_sort_by_name_descending()
    {
        $role = Role::factory()->has(Permission::factory()->count(5))->create();
        $user = $this->getUserHasPermission(PermissionType::SHOW_ROLE_PERMISSION);
        Sanctum::actingAs($user, ['']);
        $response = $this->getJson('admin/roles/' . $role->id . '/permissions?sort=-name');
        $response->assertStatus(Response::HTTP_OK);
        $permissions = $role->permissions()->orderByDesc('name')->first();
        $content = json_decode($response->getContent());
        $this->assertEquals($permissions->name, $content->data[0]->name);
    }

    /**
     * @test
     */
    public function get_permissions_to_role_sort_by_description_ascending()
    {
        $role = Role::factory()->has(Permission::factory()->count(5))->create();
        $user = $this->getUserHasPermission(PermissionType::SHOW_ROLE_PERMISSION);
        Sanctum::actingAs($user, ['']);
        $response = $this->getJson('admin/roles/' . $role->id . '/permissions?sort=description');
        $response->assertStatus(Response::HTTP_OK);
        $permissions = $role->permissions()->orderBy('description')->first();
        $content = json_decode($response->getContent());
        $this->assertEquals($permissions->description, $content->data[0]->description);
    }

    /**
     * @test
     */
    public function get_permissions_to_role_sort_by_description_descending()
    {
        $role = Role::factory()->has(Permission::factory()->count(5))->create();
        $user = $this->getUserHasPermission(PermissionType::SHOW_ROLE_PERMISSION);
        Sanctum::actingAs($user, ['']);
        $response = $this->getJson('admin/roles/' . $role->id . '/permissions?sort=-description');
        $response->assertStatus(Response::HTTP_OK);
        $permissions = $role->permissions()->orderByDesc('description')->first();
        $content = json_decode($response->getContent());
        $this->assertEquals($permissions->description, $content->data[0]->description);
    }

    /**
     * @test
     */
    public function it_gets_the_description_for_valid_values()
    {
        $indexUserDescription = PermissionType::getDescription(PermissionType::INDEX_USER->value);
        $showUserDescription = PermissionType::getDescription(PermissionType::SHOW_USER->value);
        $storeUserDescription = PermissionType::getDescription(PermissionType::STORE_USER->value);

        $this->assertEquals('I N D E X U S E R', $indexUserDescription);
        $this->assertEquals('S H O W U S E R', $showUserDescription);
        $this->assertEquals('S T O R E U S E R', $storeUserDescription);
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
                //                                'previous' => 'http://localhost/api/v1/roles/1/permissions?page=1',
                //                                'next' => 'http://localhost/api/v1/roles/1/permissions?page=3',
                //                            ]
            ],
            ]];
    }
}
