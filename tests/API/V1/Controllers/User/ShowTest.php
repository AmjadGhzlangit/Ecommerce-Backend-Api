<?php

namespace Tests\API\V1\Controllers\User;

use App\Enums\PermissionType;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\Sanctum;
use Tests\API\V1\V1TestCase;
use Tests\Helper;

class ShowTest extends V1TestCase
{
    use Helper;

    private const JSON_STRUCTURE = [
        'data' => [
            'id',
            'first_name',
            'last_name',
            'email',
            'country_id',
            'phone',
            'has_verified_email',
            'has_verified_phone',

        ],
    ];

    /**
     * @test
     */
    public function get_user_by_id_by_user_not_has_permission()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $userLogin = User::factory()->create();
        Sanctum::actingAs($userLogin);
        $response = $this->getJson('admin/users/' . $user->id);
        $response->assertStatus(403)
            ->assertJson([
                'message' => __('auth.permission_required'),
                'status_code' => 403,
            ]);
    }

    /**
     * @test
     */
    public function get_user_by_id_by_user_has_permission_show()
    {
        $image = UploadedFile::fake()->image('test_image.jpg');
        $imageHashName = $image->hashName();
        /** @var User $user */
        $user = User::factory()->create([
            'image' => 'https://dev.alebdaa.net/storage/images/users/' . $imageHashName,
        ]);
        $userLogin = $this->getUserHasPermission(PermissionType::SHOW_USER);
        Sanctum::actingAs($userLogin);
        $response = $this->get('admin/users/' . $user->id);

        $response->assertStatus(200)
            ->assertJsonStructure(self::JSON_STRUCTURE);
        $this->saveResponseToFile($response, 'admin/users/show.json');
    }

    /**
     * @test
     */
    public function get_user_with_correct_responses()
    {
        $image = UploadedFile::fake()->image('test_image.jpg');
        $imageHashName = $image->hashName();
        /** @var User $user */
        $user = User::factory()->create([
            'id' => 1,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'test1@gmail.com',
            'phone' => '+9639487222',
            'image' => 'users/' . $imageHashName,
            'country_id' => 1,
            'password' => '123456',
        ]);
        $userLogin = $this->getUserHasPermission(PermissionType::SHOW_USER);
        Sanctum::actingAs($userLogin);
        $response = $this->get('admin/users/' . $user->id);

        $response->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'id' => 1,
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'email' => 'test1@gmail.com',
                    'phone' => '+9639487222',
                    'image' => 'http://localhost/storage/users/users/' . $imageHashName,
                    'country_id' => 1,
                    'has_verified_email' => true,
                    'has_verified_phone' => true,
                ],
                'message' => 'success',
                'status_code' => 200,
            ]);
    }
}
