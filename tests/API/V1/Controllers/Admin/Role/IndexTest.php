<?php

namespace Tests\API\V1\Controllers\Admin\Role;

use App\Enums\PermissionType;
use App\Enums\RoleType;
use App\Models\Role;
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
    public function get_all_roles_by_user()
    {
        Role::factory()->count(15)->create();
        $user = $this->getUserHasPermission(PermissionType::INDEX_ROLE);
        Sanctum::actingAs($user);
        $response = $this->get('admin/roles?page=2&per_page=5');
        $response->assertStatus(200)->assertJsonStructure(self::JSON_STRUCTURE)
            ->assertJsonCount(5, 'data');
        $this->saveResponseToFile($response, 'admin/roles/index.json');
    }

    /**
     * @test
     */
    public function get_all_roles_by_user_has_permission_show()
    {
        Role::factory()->count(15)->create();
        $user = $this->getUserHasPermission(PermissionType::INDEX_ROLE);
        Sanctum::actingAs($user);
        $response = $this->get('admin/roles?page=2&per_page=5');
        $role = Role::all()->get(6);
        $response->assertStatus(200)->assertJsonStructure(self::JSON_STRUCTURE)
            ->assertJsonCount(5, 'data');
    }

    /**
     * @test
     */
    public function get_all_roles_by_user_not_authorized()
    {
        Role::factory()->count(2)->create();
        $response = $this->getJson('admin/roles?page=1&per_page=5');
        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthorized', 'status_code' => 401]);
    }

    /**
     * @test
     */
    public function get_all_roles_by_user_not_has_permission()
    {
        Role::factory()->count(2)->create();
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $response = $this->get('admin/roles?page=1&per_page=5');
        $response->assertStatus(403)
            ->assertJson(['message' => __('auth.permission_required'), 'status_code' => 403]);
    }

    /**
     * @test
     */
    public function get_all_roles_filter_by_id()
    {
        $user = $this->getUserHasPermission(PermissionType::INDEX_ROLE);
        Role::factory()->create([
            'id' => 789654321,
        ]);
        Role::factory()->create([
            'id' => 195731,
        ]);
        Sanctum::actingAs($user);
        $response = $this->get('admin/roles?filter[id]=195731');
        $response->assertStatus(200)->assertJsonStructure(self::JSON_STRUCTURE)
            ->assertJsonCount(1, 'data');
    }

    /**
     * @test
     */
    public function get_all_roles_filter_by_name()
    {
        Role::factory()->count(10)->create();
        Role::factory()->create([
            'name' => 'test',
        ]);
        $user = $this->getUserHasPermission(PermissionType::INDEX_ROLE);
        Sanctum::actingAs($user);
        $response = $this->get('admin/roles?filter[name]=test');
        $response->assertStatus(200)->assertJsonStructure(self::JSON_STRUCTURE)
            ->assertJsonCount(1, 'data');
    }

    /**
     * @test
     */
    public function get_all_roles_filter_by_description()
    {
        Role::factory()->count(10)->create();
        Role::factory()->create([
            'description' => 'test',
        ]);
        $user = $this->getUserHasPermission(PermissionType::INDEX_ROLE);
        Sanctum::actingAs($user, ['']);
        $response = $this->get('admin/roles?filter[description]=test');
        $response->assertStatus(200)->assertJsonStructure(self::JSON_STRUCTURE)
            ->assertJsonCount(1, 'data');
    }

    /**
     * @test
     */
    public function get_all_roles_sort_by_id_ascending()
    {
        Role::factory()->count(10)->create();
        $user = $this->getUserHasPermission(PermissionType::INDEX_ROLE);
        Sanctum::actingAs($user, ['']);
        $response = $this->get('admin/roles?sort=id');
        $response->assertStatus(200)->assertJsonStructure(self::JSON_STRUCTURE);
        $role = Role::orderBy('id')->first();
        $content = json_decode($response->getContent());
        $this->assertEquals($role->id, $content->data[0]->id);
    }

    /**
     * @test
     */
    public function get_all_roles_sort_by_id_descending()
    {
        Role::factory()->count(10)->create();
        $user = $this->getUserHasPermission(PermissionType::INDEX_ROLE);
        Sanctum::actingAs($user, ['']);
        $response = $this->get('admin/roles?sort=-id');
        $response->assertStatus(200)->assertJsonStructure(self::JSON_STRUCTURE);
        $role = Role::orderByDesc('id')->first();
        $content = json_decode($response->getContent());
        $this->assertEquals($role->id, $content->data[0]->id);
    }

    /**
     * @test
     */
    public function get_all_roles_sort_by_description_ascending()
    {
        Role::factory()->count(10)->create();
        $user = $this->getUserHasPermission(PermissionType::INDEX_ROLE);
        Sanctum::actingAs($user, ['']);
        $response = $this->get('admin/roles?sort=description');
        $response->assertStatus(200)->assertJsonStructure(self::JSON_STRUCTURE);
        $role = Role::orderBy('description')->first();
        $content = json_decode($response->getContent());
        $this->assertEquals($role->description, $content->data[0]->description);
    }

    /**
     * @test
     */
    public function get_all_roles_sort_by_description_descending()
    {
        Role::factory()->count(10)->create();
        $user = $this->getUserHasPermission(PermissionType::INDEX_ROLE);
        Sanctum::actingAs($user, ['']);
        $response = $this->get('admin/roles?sort=-description');
        $response->assertStatus(200)->assertJsonStructure(self::JSON_STRUCTURE);
        $role = Role::orderByDesc('description')->first();
        $content = json_decode($response->getContent());
        $this->assertEquals($role->description, $content->data[0]->description);
    }

    /**
     * @test
     */
    public function get_all_roles_sort_by_name_ascending()
    {
        Role::factory()->count(10)->create();
        $user = $this->getUserHasPermission(PermissionType::INDEX_ROLE);
        Sanctum::actingAs($user, ['']);
        $response = $this->get('admin/roles?sort=name');
        $response->assertStatus(200)->assertJsonStructure(self::JSON_STRUCTURE);
        $role = Role::orderBy('name')->first();
        $content = json_decode($response->getContent());
        $this->assertEquals($role->name, $content->data[0]->name);
    }

    /**
     * @test
     */
    public function get_all_roles_sort_by_name_descending()
    {
        Role::factory()->count(10)->create();
        $user = $this->getUserHasPermission(PermissionType::INDEX_ROLE);
        Sanctum::actingAs($user, ['']);
        $response = $this->get('admin/roles?sort=-name');
        $response->assertStatus(200)->assertJsonStructure(self::JSON_STRUCTURE);
        $role = Role::orderByDesc('name')->first();
        $content = json_decode($response->getContent());
        $this->assertEquals($role->name, $content->data[0]->name);
    }

    /**
     * @test
     */
    public function it_gets_the_description_for_valid_values()
    {
        $superAdminDescription = RoleType::getDescription(RoleType::SUPER_ADMIN->value);
        $databaseDescription = RoleType::getDescription(RoleType::DATABASE->value);
        $operationDescription = RoleType::getDescription(RoleType::OPERATION->value);

        $this->assertEquals('S U P E R A D M I N', $superAdminDescription);
        $this->assertEquals('D A T A B A S E', $databaseDescription);
        $this->assertEquals('O P E R A T I O N', $operationDescription);
    }

    /**
     * @test
     */
    public function it_returns_an_empty_string_for_invalid_values()
    {
        $invalidDescription = RoleType::getDescription('');

        $this->assertEquals('', $invalidDescription);
    }

    /**
     * @test
     */
    public function if_structure_response_is_correct()
    {
        Role::factory()->create([
            'id' => 1,
            'name' => 'test',
            'description' => 'This Description For Testing',
            'guard_name' => 'api',
        ]);
        Role::factory()->count(15)->create();
        $user = $this->getUserHasPermission(PermissionType::INDEX_ROLE);
        Sanctum::actingAs($user, ['']);
        $response = $this->get('admin/roles?sort=id');
        $response->assertStatus(200)->assertJsonStructure(self::JSON_STRUCTURE);
        $content = json_decode($response->getContent());

        $this->assertJsonStringEqualsJsonString(json_encode($content->data[0]), json_encode([
            'id' => 1,
            'name' => 'test',
            'description' => 'This Description For Testing',
        ]));

    }
}
