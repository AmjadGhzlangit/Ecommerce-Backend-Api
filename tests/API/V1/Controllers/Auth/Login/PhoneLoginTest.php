<?php

namespace Tests\API\V1\Controllers\Auth;

use App\Enums\PermissionType;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Tests\API\V1\V1TestCase;

class PhoneLoginTest extends V1TestCase
{
    private const JSON_STRUCTURE = [
        'data' => [
            'token_type',
            'access_token',
            'access_expires_at',
            'profile' => [
                'id',
                'first_name',
                'last_name',
                'email',
                'has_verified_email',
                'phone',
                'has_verified_phone',
                'image',

            ],
        ],
    ];

    /**
     * @test
     */
    public function phone_is_required()
    {
        $userData = $this->data();
        $this->getUserHasPermission([PermissionType::INDEX_USER], $userData);
        $response = $this->postJson('auth/phone-login', array_merge($this->data(), ['phone' => '']));
        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'data' => [
                    'phone' => ['The phone field is required.'],
                ],
                'status_code' => 422,
            ]);
    }

    /**
     * @test
     */
    public function password_is_required()
    {
        $userData = $this->data();
        $this->getUserHasPermission([], $userData);
        $response = $this->postJson('auth/phone-login', array_merge($this->data(), ['password' => '']));
        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'data' => [
                    'password' => ['The password field is required.'],
                ],
                'status_code' => 422,
            ]);
    }

    /**
     * @test
     */
    public function login_not_authenticated_with_wrong_phone()
    {
        $userData = $this->data();
        User::factory()->create($userData);
        $response = $this->postJson('auth/phone-login', array_merge($this->data(), ['phone' => 'phone@wrong.test']));
        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'data' => [
                    'phone' => ['The selected phone is invalid.'],
                ],
                'status_code' => 422,
            ]);
    }

    /**
     * @test
     */
    public function login_not_authenticated_with_wrong_password()
    {
        $userData = $this->data();

        User::factory()->create($userData);
        $response = $this->postJson('auth/phone-login', array_merge($this->data(), ['password' => 'wrongPassword']));
        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'data' => [
                    'phone' => ['The provided credentials are incorrect.'],
                ],
                'status_code' => 422,
            ]);
    }

    /**
     * @test
     */
    public function it_requires_fcm_token_if_udid_present_in_login()
    {
        $userData = $this->data();
        User::factory()->create($userData);
        $response = $this->postJson('auth/phone-login', array_merge($userData, ['udid' => '12345689']));

        $response->assertStatus(422)
            ->assertJson([
                'data' => [
                    'fcm_token' => ['The fcm token field is required when udid is present.'],
                ],
            ]);
    }

    /**
     * @test
     */
    public function login_admin_with_phone()
    {
        $image = UploadedFile::fake()->image('test_image.jpg');
        $imageHashName = $image->hashName();
        $userData = $this->data();
        $user = User::factory()->create([
            'phone' => '+971585422373',
            'password' => 'secret',
            'image' => 'images/users/' . $imageHashName,
        ]);
        /*ANY PERMISSION JUST TO MAKE SURE THAT THE RESPONSE RETURNING THE CORRECT DATA*/
        $user = $this->getUserHasPermission([PermissionType::INDEX_USER], $userData);

        /*ANY PERMISSION JUST TO MAKE SURE THAT THE RESPONSE RETURNING THE CORRECT DATA*/
        $response = $this->postJson('auth/phone-login', array_merge($userData, ['udid' => '12345689','fcm_token' => 123456789]));
        $response->assertStatus(200);
        $response->assertJsonStructure(self::JSON_STRUCTURE);

        $this->saveResponseToFile($response, 'auth/login/phone-login.json');
    }

    private function data(): array
    {
        return [
            'phone' => '+971585422373',
            'password' => 'secret',
        ];
    }
}
