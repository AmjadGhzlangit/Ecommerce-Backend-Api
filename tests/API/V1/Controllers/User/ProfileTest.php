<?php

namespace Tests\API\V1\Controllers\User;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\Sanctum;
use Tests\API\V1\V1TestCase;

class ProfileTest extends V1TestCase
{
    private const JSON_STRUCTURE = [
        'data' => [
            'id',
            'first_name',
            'last_name',
            'email',
            'phone',
            'has_verified_email',
            'has_verified_phone',
        ],
    ];

    /**
     * @test
     */
    public function get_user_profile_without_access_token()
    {
        User::factory()->create();
        $response = $this->getJson('admin/profile', [
            'Accept' => 'application/json',
        ]);
        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthorized',
                'status_code' => 401,
            ]);
    }

    /**
     * @test
     */
    public function get_user_profile_with_fault_access_token()
    {
        User::factory()->create();
        $response = $this->getJson('admin/profile', [
            'Authorization' => 'Bearer ' . 'test_fake_token',
            'Accept' => 'application/json',
        ]);
        $response
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthorized',
                'status_code' => 401,
            ]);
    }

    /**
     * @test
     */
    public function get_user_profile()
    {
        $image = UploadedFile::fake()->image('test_image.jpg');
        $imageHashName = $image->hashName();
        $user = User::factory()->create([
            'id' => 1,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'image' => 'users/' . $imageHashName,
            'email' => 'test1@gmail.com',
            'phone' => '+9639487222',
            'country_id' => 1,
            'password' => '123456',
        ]);
        Sanctum::actingAs($user);
        $response = $this->get('admin/profile');
        $response->assertStatus(200)
            ->assertJsonStructure(self::JSON_STRUCTURE)
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
        $this->saveResponseToFile($response, 'admin/users/profile.json');
    }
}
