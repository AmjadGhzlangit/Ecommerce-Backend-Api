<?php

namespace Tests\API\V1\Controllers\User;

use App\Enums\PermissionType;
use App\Models\Country;
use App\Models\Device;
use App\Models\User;
use App\Models\UserPaymentMethod;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\Sanctum;
use Tests\API\V1\V1TestCase;

class IndexTest extends V1TestCase
{
    private const JSON_STRUCTURE = [
        'data' => [
            '*' => [
                'id',
                'first_name',
                'last_name',
                'email',
                'phone',
                'has_verified_email',
                'has_verified_phone',

            ],
        ],
    ];

    /**
     * @test
     */
    public function get_all_users_by_user_not_has_permission()
    {
        User::factory()->count(2)->create();
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $response = $this->get('admin/users?page=1&per_page=5');
        $response->assertStatus(403)
            ->assertJson([
                'message' => __('auth.permission_required'),
                'status_code' => 403,
            ]);
    }

    /**
     * @test
     */
    public function get_all_users_by_user_has_permission_show()
    {
        User::factory()->count(15)->create();
        $user = $this->getUserHasPermission(PermissionType::INDEX_USER);
        Sanctum::actingAs($user, ['']);
        $response = $this->get('admin/users?page=2&per_page=5');
        $response->assertStatus(200)->assertJsonStructure(self::JSON_STRUCTURE);
        $response->assertJsonCount(5, 'data');

        $response->assertJson($this->dataResponsePagination(16));
        $this->saveResponseToFile($response, 'admin/users/index.json');
    }

    /**
     * @test
     */
    public function get_all_users_filter_by_id()
    {
        User::factory()->create([
            'id' => 555,
        ]);
        User::factory()->create([
            'id' => 1111111,
        ]);
        User::factory()->create([
            'id' => 11111111,
        ]);
        $user = $this->getUserHasPermission(PermissionType::INDEX_USER, [
            'id' => 666,
        ]);
        Sanctum::actingAs($user, ['']);
        $response = $this->get('admin/users?filter[id]=1111111');
        $response->assertStatus(200)->assertJsonStructure(self::JSON_STRUCTURE)
            ->assertJsonCount(1, 'data');
    }

    /**
     * @test
     */
    public function get_all_users_filter_by_first_name()
    {
        User::factory()->count(3)->create();
        User::factory()->create([
            'first_name' => 'JOHN_AMJAD',
            'last_name' => 'Doe',
        ]);
        $user = $this->getUserHasPermission(PermissionType::INDEX_USER);
        Sanctum::actingAs($user, ['']);
        $response = $this->get('admin/users?filter[first_name]=JOHN_AMJAD');
        $response->assertStatus(200)->assertJsonStructure(self::JSON_STRUCTURE)
            ->assertJsonCount(1, 'data');
    }

    /**
     * @test
     */
    public function get_all_users_filter_by_last_name()
    {
        User::factory()->count(10)->create();
        User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);
        $user = $this->getUserHasPermission(PermissionType::INDEX_USER);
        Sanctum::actingAs($user, ['']);
        $response = $this->get('admin/users?filter[last_name]=Doe');
        $response->assertStatus(200)->assertJsonStructure(self::JSON_STRUCTURE)
            ->assertJsonCount(1, 'data');
    }

    /**
     * @test
     */
    public function get_user_search_by_name(): void
    {
        $user = User::factory()->create([
            'first_name' => 'joe',
        ]);
        $user = $this->getUserHasPermission(PermissionType::INDEX_USER);
        Sanctum::actingAs($user);
        $response = $this->getJson('admin/users?filter[search]=joe');
        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function search_nonexistent_user_by_name(): void
    {
        $user = User::factory()->create();
        $user = $this->getUserHasPermission(PermissionType::INDEX_USER);
        Sanctum::actingAs($user);
        $response = $this->getJson('admin/users?filter[search]=NonExistentUser');
        $response->assertStatus(200)->assertExactJson([
            'data' => [],
            'message' => 'success',
            'meta' => [
                'pagination' => [
                    'count' => 0,
                    'current_page' => 1,
                    'per_page' => 15,
                    'total' => 0,
                ],
            ],
            'status_code' => 200,
        ]);
    }

    /**
     * @test
     */
    public function get_all_users_filter_by_email()
    {
        User::factory()->count(10)->create();
        User::factory()->create([
            'email' => 'test@test.com',
        ]);
        $user = $this->getUserHasPermission(PermissionType::INDEX_USER);
        Sanctum::actingAs($user, ['']);
        $response = $this->get('admin/users?filter[email]=test@test.com');
        $response->assertStatus(200)->assertJsonStructure(self::JSON_STRUCTURE)
            ->assertJsonCount(1, 'data');
    }

    /**
     * @test
     */
    public function get_all_users_sort_by_first_name_ascending()
    {
        User::factory()->count(10)->create();
        $user = $this->getUserHasPermission(PermissionType::INDEX_USER);
        Sanctum::actingAs($user, ['']);
        $response = $this->get('admin/users?sort=first_name');
        $response->assertStatus(200)->assertJsonStructure(self::JSON_STRUCTURE);
        $user = User::orderBy('first_name')->first();
        $content = json_decode($response->getContent());
        $this->assertEquals($user->id, $content->data[0]->id);
    }

    /**
     * @test
     */
    public function get_all_users_sort_by_first_name_descending()
    {
        User::factory()->count(10)->create();
        $user = $this->getUserHasPermission(PermissionType::INDEX_USER);
        Sanctum::actingAs($user, ['']);
        $response = $this->get('admin/users?sort=-first_name');
        $response->assertStatus(200)->assertJsonStructure(self::JSON_STRUCTURE);
        $user = User::orderByDesc('first_name')->first();
        $content = json_decode($response->getContent());
        $this->assertEquals($user->id, $content->data[0]->id);
    }

    /**
     * @test
     */
    public function get_all_users_sort_by_email_ascending()
    {
        User::factory()->count(10)->create();
        $user = $this->getUserHasPermission(PermissionType::INDEX_USER);
        Sanctum::actingAs($user, ['']);
        $response = $this->get('admin/users?sort=email');
        $response->assertStatus(200)->assertJsonStructure(self::JSON_STRUCTURE);
        $user = User::orderBy('email')->first();
        $content = json_decode($response->getContent());
        $this->assertEquals($user->email, $content->data[0]->email);
    }

    /**
     * @test
     */
    public function get_all_users_sort_by_email_descending()
    {
        User::factory()->count(10)->create();
        $user = $this->getUserHasPermission(PermissionType::INDEX_USER);
        Sanctum::actingAs($user, ['']);
        $response = $this->get('admin/users?sort=-email');
        $response->assertStatus(200)->assertJsonStructure(self::JSON_STRUCTURE);
        $user = User::orderByDesc('email')->first();
        $content = json_decode($response->getContent());
        $this->assertEquals($user->email, $content->data[0]->email);
    }

    /**
     * @test
     */
    public function get_all_users_filter_by_phone()
    {
        User::factory()->create([
            'phone' => '44376834',
        ]);
        User::factory()->create([
            'phone' => '445545454',
        ]);
        User::factory()->create([
            'phone' => '0999999999',
        ]);
        $user = $this->getUserHasPermission(PermissionType::INDEX_USER);
        Sanctum::actingAs($user, ['']);
        $response = $this->get('admin/users?filter[phone]=4437');
        $response->assertStatus(200)->assertJsonStructure(self::JSON_STRUCTURE)
            ->assertJsonCount(1, 'data');
    }

    /**
     * @test
     */
    public function a_user_belongs_to_country()
    {
        $country = Country::factory()->create();
        $user = User::factory()->create(['country_id' => $country->id]);

        $this->assertInstanceOf(Country::class, $user->country);
        $this->assertEquals($country->id, $user->country->id);
    }
    /**
     * @test
     */
    public function device_belongs_to_a_user()
    {
        $user = User::factory()->create();

        $device = Device::factory()->create(['user_id' => $user->id]);

        $associatedUser = $device->user;

        $this->assertInstanceOf(User::class, $associatedUser);
        $this->assertEquals($user->id, $associatedUser->id);
    }

    /**
     * @test
     */
    public function if_structure_response_is_correct()
    {
        $image = UploadedFile::fake()->image('test_image.jpg');
        $imageHashName = $image->hashName();
        User::factory()->create([
            'id' => 1,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'test1@gmail.com',
            'phone' => '+9639487222',
            'image' => $imageHashName,
            'country_id' => 1,
            'password' => '123456',
        ]);
        User::factory()->count(15)->create();
        $user = $this->getUserHasPermission(PermissionType::INDEX_USER);
        Sanctum::actingAs($user, ['']);
        $response = $this->get('admin/users?sort=id');
        $response->assertStatus(200)->assertJsonStructure(self::JSON_STRUCTURE);
        $content = json_decode($response->getContent());

        $this->assertJsonStringEqualsJsonString(json_encode($content->data[0]), json_encode([
            'id' => 1,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'test1@gmail.com',
            'phone' => '+9639487222',
            'country_id' => 1,
            'image' => 'http://localhost/storage/users/' . $imageHashName,
            'has_verified_email' => true,
            'has_verified_phone' => true,

        ])
        );
    }

    private function dataResponsePagination($total)
    {
        return
            ['meta' => ['pagination' => [
                'total' => $total,
                'count' => 5,
                'per_page' => 5,
                'current_page' => 2,
            ],
            ]];
    }
}
