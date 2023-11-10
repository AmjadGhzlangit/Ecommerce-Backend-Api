<?php

namespace Tests\API\V1\Controllers\Admin\Permission;

use App\Enums\PermissionType;
use App\Models\Permission;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\API\V1\V1TestCase;

class IndexTest extends V1TestCase
{
    private const JSON_STRUCTURE = [
        'data' => [
            '*' => [
                'id',
                'name',
                'description',
            ],
        ],
    ];

    /**
     * @test
     */
    public function get_permissions_by_user_not_authorized()
    {
        $response = $this->getJson('admin/permissions?page=2&per_page=5');
        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthorized', 'status_code' => 401]);
    }

    /**
     * @test
     */
    public function get_permissions_by_user_has_not_role()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['']);
        $response = $this->getJson('admin/permissions?page=2&per_page=5');
        $response->assertStatus(403)
            ->assertJson([
                'message' => __('auth.permission_required'),
                'status_code' => 403,
            ]);
    }

    /**
     * @test
     */
    public function get_all_permissions()
    {
        Permission::factory()->count(15)->create();
        $user = $this->getUserHasPermission(PermissionType::SHOW_PERMISSIONS);
        Sanctum::actingAs($user, ['']);
        $response = $this->getJson('admin/permissions?page=2&per_page=5');
        $response->assertStatus(200)->assertJsonStructure(self::JSON_STRUCTURE)
            ->assertJsonCount(5, 'data');
        $this->saveResponseToFile($response, 'admin/permissions/index.json');
    }

    /**
     * @test
     */
    public function permission_filter_id()
    {
        Permission::factory()->create([
            'id' => 11,
        ]);
        Permission::factory()->create([
            'id' => 111,
        ]);
        Permission::factory()->create([
            'id' => 59909999,
        ]);
        $user = $this->getUserHasPermission(PermissionType::SHOW_PERMISSIONS);
        Sanctum::actingAs($user, ['']);
        $response = $this->getJson('admin/permissions?filter[id]=59909999');
        $response->assertStatus(200)->assertJsonStructure(self::JSON_STRUCTURE)
            ->assertJsonCount(1, 'data');
    }

    /**
     * @test
     */
    public function permission_filter_name()
    {
        Permission::factory()->count(1)->create([
            'name' => 'test',
        ]);
        Permission::factory()->count(10)->create();
        $user = $this->getUserHasPermission(PermissionType::SHOW_PERMISSIONS);
        Sanctum::actingAs($user, ['']);
        $response = $this->getJson('admin/permissions?filter[name]=test');
        $response->assertStatus(200)->assertJsonStructure(self::JSON_STRUCTURE)
            ->assertJsonCount(1, 'data');
    }

    /**
     * @test
     */
    public function permission_filter_description()
    {
        Permission::factory()->count(3)->create([
            'description' => 'test',
        ]);
        Permission::factory()->count(10)->create([
            'description' => 'description',
        ]);
        $user = $this->getUserHasPermission(PermissionType::SHOW_PERMISSIONS);
        Sanctum::actingAs($user, ['']);
        $response = $this->getJson('admin/permissions?filter[description]=test');
        $response->assertStatus(200)->assertJsonStructure(self::JSON_STRUCTURE)
            ->assertJsonCount(3, 'data');
    }

    public function permission_filter_id_and_name_and_description()
    {
        Permission::factory()->create([
            'id' => '1',
            'name' => 'name',
            'description' => 'description',
        ]);
        Permission::factory()->count(10)->create();
        $user = $this->getUserHasPermission(PermissionType::SHOW_PERMISSIONS);
        Sanctum::actingAs($user, ['']);
        $response = $this->getJson('admin/permissions?filter[id]=1&filter[name]=name&filter[description]=description');
        $response->assertStatus(200)->assertJsonStructure(self::JSON_STRUCTURE)
            ->assertJsonCount(1, 'data');
    }

    /**
     * @test
     */
    public function permission_filter_id_and_name()
    {
        Permission::factory()->create([
            'id' => '1',
            'name' => 'name',
        ]);
        Permission::factory()->count(10)->create();
        $user = $this->getUserHasPermission(PermissionType::SHOW_PERMISSIONS);
        Sanctum::actingAs($user, ['']);
        $response = $this->getJson('admin/permissions?filter[id]=1&filter[name]=name');
        $response->assertStatus(200)->assertJsonStructure(self::JSON_STRUCTURE)
            ->assertJsonCount(1, 'data');
    }

    /**
     * @test
     */
    public function permission_filter_id_and_description()
    {
        Permission::factory()->create([
            'id' => '1',
            'name' => 'name',
            'description' => 'description',
        ]);
        Permission::factory()->count(10)->create();
        $user = $this->getUserHasPermission(PermissionType::SHOW_PERMISSIONS);
        Sanctum::actingAs($user, ['']);
        $response = $this->getJson('admin/permissions?filter[id]=1&filter[description]=description');
        $response->assertStatus(200)->assertJsonStructure(self::JSON_STRUCTURE)
            ->assertJsonCount(1, 'data');
    }

    /**
     * @test
     */
    public function permission_filter_name_and_description()
    {
        Permission::factory()->create([
            'id' => '1',
            'name' => 'name',
            'description' => 'description',
        ]);
        Permission::factory()->count(10)->create();
        $user = $this->getUserHasPermission(PermissionType::SHOW_PERMISSIONS);
        Sanctum::actingAs($user, ['']);
        $response = $this->getJson('admin/permissions?filter[name]=name&filter[description]=description');
        $response->assertStatus(200)->assertJsonStructure(self::JSON_STRUCTURE)
            ->assertJsonCount(1, 'data');
    }

    /**
     * @test
     */
    public function permission_sort_by_id_ascending()
    {
        Permission::factory()->count(10)->create();
        $user = $this->getUserHasPermission(PermissionType::SHOW_PERMISSIONS);
        Sanctum::actingAs($user, ['']);
        $response = $this->getJson('admin/permissions?sort=id');
        $response->assertStatus(200)->assertJsonStructure(self::JSON_STRUCTURE);
        $permissions = Permission::orderBy('id')->first();
        $content = json_decode($response->getContent());
        $this->assertEquals($permissions->id, $content->data[0]->id);
    }

    /**
     * @test
     */
    public function permission_sort_by_id_descending()
    {
        Permission::factory()->count(10)->create();
        $user = $this->getUserHasPermission(PermissionType::SHOW_PERMISSIONS);
        Sanctum::actingAs($user, ['']);
        $response = $this->getJson('admin/permissions?sort=-id');
        $response->assertStatus(200)->assertJsonStructure(self::JSON_STRUCTURE);
        $permissions = Permission::orderByDesc('id')->first();
        $content = json_decode($response->getContent());
        $this->assertEquals($permissions->id, $content->data[0]->id);
    }

    /**
     * @test
     */
    public function permission_sort_by_name_ascending()
    {
        Permission::factory()->count(10)->create();
        $user = $this->getUserHasPermission(PermissionType::SHOW_PERMISSIONS);
        Sanctum::actingAs($user, ['']);
        $response = $this->getJson('admin/permissions?sort=name');
        $response->assertStatus(200)->assertJsonStructure(self::JSON_STRUCTURE);
        $permissions = Permission::orderBy('name')->first();
        $content = json_decode($response->getContent());
        $this->assertEquals($permissions->id, $content->data[0]->id);
    }

    /**
     * @test
     */
    public function permission_sort_by_name_descending()
    {
        Permission::factory()->count(10)->create();
        $user = $this->getUserHasPermission(PermissionType::SHOW_PERMISSIONS);
        Sanctum::actingAs($user, ['']);
        $response = $this->getJson('admin/permissions?sort=-name');
        $response->assertStatus(200)->assertJsonStructure(self::JSON_STRUCTURE);
        $permissions = Permission::orderByDesc('name')->first();
        $content = json_decode($response->getContent());
        $this->assertEquals($permissions->id, $content->data[0]->id);
    }

    /**
     * @test
     */
    public function if_structure_response_is_correct()
    {
        Permission::factory()->create([
            'id' => 1,
            'name' => 'test',
            'description' => 'This Description Permission For Testing',
        ]);
        Permission::factory()->count(15)->create();
        $user = $this->getUserHasPermission(PermissionType::SHOW_PERMISSIONS);
        Sanctum::actingAs($user, ['']);
        $response = $this->get('admin/permissions?sort=id');
        $response->assertStatus(200)->assertJsonStructure(self::JSON_STRUCTURE);
        $content = json_decode($response->getContent());

        $this->assertJsonStringEqualsJsonString(json_encode($content->data[0]), json_encode([
            'id' => 1,
            'name' => 'test',
            'description' => 'This Description Permission For Testing',
        ]));
    }
}
