<?php

namespace Tests\API\V1\Controllers\Auth;

use App\Models\Country;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\Sanctum;
use Tests\API\V1\V1TestCase;

class UpdateTest extends V1TestCase
{
    use RefreshDatabase;

    private const JSON_STRUCTURE = [
            'data' => [
                'id',
                'first_name',
                'last_name',
                'email',
                'image',
                'has_verified_email',
                'phone',
                'country_id',
                'has_verified_phone',
            ],
    ];

    /**
     * @test
     */
    public function update_user_information_by_user_not_authorized()
    {
        $user = User::factory()->create([
        'email' => 'amjad@example.com',
    ]);
        $newData = [
            'first_name' => 'amjad',
            'last_name' => 'ghlan',
            'email' => 'johndoe@example.com',
        ];

        $response = $this->putJson('auth/update/', $newData);
        $this->withExceptionHandling();
        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthorized',
                'status_code' => 401,
            ]);
        $this->assertDatabaseMissing('users', $newData);
    }

    /**
     * @test
     */
    public function update_user_information_successfully()
    {
        $country = Country::factory()->create();
        $user = User::factory()->create([
        'first_name' => 'old first_name',
        'last_name' => 'old last_name',
        'email' => 'amjad@example.com',

        'phone' => '05012345678',

        ]);
        $newData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'johndoe@example.com',
            'country_id' => $country->id,
            'phone' => '0501234567',
        ];

        $image = UploadedFile::fake()->image('test_image.jpg');
        $imageHashName = $image->hashName();

        Sanctum::actingAs($user);
        $response = $this->putJson('auth/update/', array_merge($newData, ['image' => $image]));

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Your information updated successfully',
                'status_code' => 200,
            ]);
        $response->assertStatus(200)->assertJsonStructure(self::JSON_STRUCTURE);

        $userWithImage = [
            'email' => $newData['email'],
        ];
        $this->assertDatabaseHas('users', $userWithImage);
        $this->assertDatabaseCount('users', 1);

        $lastUser = User::latest()->first();
        $this->assertEquals($user['id'], $lastUser->id);

        $this->saveResponseToFile($response, 'auth/update.json');
    }

    /**
     * @test
     */
    public function validation_for_maximum_lengths()
    {
        $country = Country::factory()->create();
        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'amjad@example.com',
            'phone' => '05012345678',
        ]);
        $tooLongFirstName = str_repeat('a', 256);
        $tooLongLastName = str_repeat('b', 256);
        $tooLongEmail = str_repeat('c', 256) . '@example.com';

        Sanctum::actingAs($user);
        $response = $this->putJson('auth/update/', [
                'first_name' => $tooLongFirstName,
                'last_name' => $tooLongLastName,
                'email' => $tooLongEmail,
                'country_id' => $country->id,
            ]);

        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function validation_for_country_id()
    {
        $country = Country::factory()->create(
            ['id' => 1]
        );
        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'amjad@example.com',

            'phone' => '05012345678',
        ]);

        Sanctum::actingAs($user);
        $response = $this->putJson('auth/update/', [
                'country_id' => 5,
            ]);

        $response->assertStatus(422);
    }
}
