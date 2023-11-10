<?php

namespace Tests\API\V1\Controllers\Auth;

use App\Models\Country;
use Tests\API\V1\V1TestCase;

class RegisterTest extends V1TestCase
{
    private const JSON_STRUCTURE = [
        'data' => [
            'id',
            'first_name',
            'last_name',
            'email',
            'has_verified_email',
            'phone',
            'has_verified_phone',

        ],
    ];

    /**
     * @test
     */
    public function register_user_successfully_with_only_email()
    {
        $userData = $this->data();
        $response = $this->postJson('auth/register', array_merge($userData, ['phone' => '']));
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'User registered successfully',
            ])->assertJsonStructure(self::JSON_STRUCTURE);

        $this->assertDatabaseHas('users', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'johndoe@example.com',
        ]);
        $this->saveResponseToFile($response, 'auth/register.json');
    }

    /**
     * @test
     */
    public function register_user_successfully_with_only_phone()
    {
        $userData = $this->data();
        $response = $this->postJson('auth/register', array_merge($userData, ['email' => '']));

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'User registered successfully',
            ])->assertJsonStructure(self::JSON_STRUCTURE);

        $this->assertDatabaseHas('users', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '0501234567',
        ]);
        $this->saveResponseToFile($response, 'auth/phone_register.json');
    }

    /**
     * @test
     */
    public function register_user_successfully_if_udid_present_in_registration()
    {
        $userData = $this->data();
        // Remove 'udid' and 'fcm_token' from the data
        unset($userData['udid']);
        unset($userData['fcm_token']);

        $response = $this->postJson('auth/register', $userData);
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'User registered successfully',
            ])->assertJsonStructure(self::JSON_STRUCTURE);

        $this->assertDatabaseHas('users', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'johndoe@example.com',
        ]);
    }

    /**
     * @test
     */
    public function user_first_name_is_required_in_registration()
    {
        $userData = $this->data();
        $response = $this->postJson('auth/register', array_merge($userData, ['first_name' => '']));
        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'data' => [
                    'first_name' => ['The first name field is required.'],
                ],
                'status_code' => 422,
            ]);
    }

    /**
     * @test
     */
    public function user_last_name_is_required_in_registration()
    {
        $userData = $this->data();
        $response = $this->postJson('auth/register', array_merge($userData, ['last_name' => '']));
        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'data' => [
                    'last_name' => ['The last name field is required.'],
                ],
                'status_code' => 422,
            ]);
    }

    /**
     * @test
     */
    public function user_email_is_required_when_phone_is_not_present_in_registration()
    {
        $country = Country::factory()->create();
        $userData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => '',
            'phone' => '',
            'country_id' => $country->id,
            'password' => 'password123',
        ];
        $response = $this->postJson('auth/register', $userData);
        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'data' => [
                    'email' => ['The email field is required when phone is empty.'],
                ],
                'status_code' => 422,
            ]);
    }

    /**
     * @test
     */
    public function user_password_is_required_in_registration()
    {
        $userData = $this->data();
        $response = $this->postJson('auth/register', array_merge($userData, ['password' => '']));
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
    public function validation_for_country_id()
    {
        $userData = $this->data();
        $response = $this->postJson('auth/register', array_merge($userData, ['country_id' => 5]));
        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'data' => [
                    'country_id' => ['The selected country id is invalid.'],
                ],
                'status_code' => 422,
            ]);
    }

    /**
     * @test
     */
    public function it_requires_fcm_token_if_udid_present_in_register()
    {
        $userData = $this->data();
        $response = $this->postJson('auth/register', array_merge($userData, ['fcm_token' => '']));

        $response->assertStatus(422)
            ->assertJson([
                'data' => [
                    'fcm_token' => ['The fcm token field is required when udid is present.'],
                ],
            ]);
    }

    private function data(): array
    {
        $country = Country::factory()->create();

        return [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'johndoe@example.com',
                'phone' => '0501234567',
                'country_id' => $country->id,
                'password' => 'password123',
                'firebase_uid' => 123456789,
                'udid' => '123456879',
                'fcm_token' => '21354987',
        ];
    }
}
