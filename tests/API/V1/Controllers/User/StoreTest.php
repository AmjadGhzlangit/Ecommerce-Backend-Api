<?php

namespace Tests\API\V1\Controllers\User;

use App\Enums\PermissionType;
use App\Models\Country;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\Sanctum;
use Tests\API\V1\V1TestCase;
use Tests\Helper;

class StoreTest extends V1TestCase
{
    use Helper;

    /**
     * @test
     */
    public function add_user_by_user_not_authorized()
    {
        $response = $this->postJson('admin/users', [
            'first_name' => 'Test',
            'last_name' => 'Test',
            'age' => '25',
            'email' => 'test@gmail.com',
            'phone' => '+9639487222',
            'password' => '123456',
            'country' => 'United Arab Emirates',
            'password_confirmation' => '123456',

        ]);
        $this->assertCount(0, User::all());
        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthorized',
                'status_code' => 401,
            ]);
    }

    /**
     * @test
     */
    public function add_user_by_user_has_not_permission()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $response = $this->postJson('admin/users', [
            'first_name' => 'Test',
            'last_name' => 'Test',
            'email' => 'test@gmail.com',
            'phone' => '+9639487222',
            'password' => '123456',
            'country' => 'United Arab Emirates',
            'password_confirmation' => '123456',

        ]);
        $this->assertCount(1, User::all());
        $response->assertStatus(403)
            ->assertJson([
                'message' => __('auth.permission_required'),
                'status_code' => 403,
            ]);
    }

    /**
     * @test
     */
    public function add_user_by_user_has_permission_store()
    {
        $country = Country::factory()->create([
            'id' => 1,
        ]);
        $userData = [
            'first_name' => 'Test',
            'last_name' => 'Test',
            'email' => 'test@gmail.com',
            'phone' => '+963994622354',
            'password' => '123456',
            'country_id' => $country->id,
            'password_confirmation' => '123456',

        ];
        $user = $this->getUserHasPermission(PermissionType::STORE_USER);

        $image = UploadedFile::fake()->image('test_image.jpg');
        Sanctum::actingAs($user);
        $response = $this->postJson('admin/users', array_merge($userData, ['image' => $image]));
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'The user added successfully',
                'status_code' => 200,
            ]);
        $this->assertCount(2, User::all());

        $this->saveResponseToFile($response, 'admin/users/store.json');
    }

    /**
     * @test
     */
    public function add_user_by_user_has_permission_update_with_required_fields()
    {
        $user = $this->getUserHasPermission(PermissionType::STORE_USER);
        Sanctum::actingAs($user);
        $response = $this->postJson('admin/users', [

        ]);

        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'data' => [
                    'first_name' => ['The first name field is required.'],
                    'last_name' => ['The last name field is required.'],
                    'email' => ['The email field is required.'],
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
        $user = $this->getUserHasPermission(PermissionType::STORE_USER);
        Country::factory()->create([
            'id' => 1,
        ]);
        Sanctum::actingAs($user);
        $response = $this->postJson('admin/users', [
            'country_id' => 5,
        ]);

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
}
