<?php

namespace Tests\API\V1\Controllers\Auth;

use App\Enums\PermissionType;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Tests\API\V1\V1TestCase;

class EmailLoginTest extends V1TestCase
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
    public function email_is_required()
    {
        $userData = $this->data();
        $response = $this->postJson('auth/email-login', array_merge($this->data(), ['email' => '']));
        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'data' => [
                    'email' => ['The email field is required.'],
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
        $response = $this->postJson('auth/email-login', array_merge($this->data(), ['password' => '']));
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
    public function login_not_authenticated_with_wrong_email()
    {
        $userData = $this->data();
        User::factory()->create($userData);
        $response = $this->postJson('auth/email-login', array_merge($this->data(), ['email' => 'email@wrong.test']));
        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'data' => [
                    'email' => ['The selected email is invalid.'],
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
        $response = $this->postJson('auth/email-login', array_merge($this->data(), ['password' => 'wrongPassword']));
        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'data' => [
                    'email' => ['The provided credentials are incorrect.'],
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
        $response = $this->postJson('auth/email-login', array_merge($userData, ['udid' => '12345689']));

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
    public function login_admin_with_email()
    {
        $image = UploadedFile::fake()->image('test_image.jpg');
        $imageHashName = $image->hashName();
        $userData = $this->data();
        $user = User::factory()->create([
            'email' => 'ddd@gmail.com',
            'password' => 'secret',
            'image' => 'images/users/' . $imageHashName,
        ]);
        /*ANY PERMISSION JUST TO MAKE SURE THAT THE RESPONSE RETURNING THE CORRECT DATA*/
        $user = $this->getUserHasPermission([PermissionType::INDEX_USER], $userData);

        /*ANY PERMISSION JUST TO MAKE SURE THAT THE RESPONSE RETURNING THE CORRECT DATA*/
        $response = $this->postJson('auth/email-login', array_merge($userData, ['udid' => '12345689','fcm_token' => 123456789]));
        $response->assertStatus(200);
        $response->assertJsonStructure(self::JSON_STRUCTURE);

        $this->saveResponseToFile($response, 'auth/login/email-login.json');
    }

    private function data(): array
    {
        return [
            'email' => 'ddd@gmail.com',
            'password' => 'secret',
        ];
    }
}
