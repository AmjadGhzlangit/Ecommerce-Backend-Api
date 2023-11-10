<?php

namespace Tests\API\V1\Controllers\User;

use App\Enums\PermissionType;
use App\Models\Country;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\API\V1\V1TestCase;
use Tests\Helper;

class UpdateTest extends V1TestCase
{
    use Helper;

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
    public function update_user_by_id_by_user_not_has_permission_update()
    {
        $user = User::factory()->create();
        $userLogin = User::factory()->create();
        Sanctum::actingAs($userLogin);
        $response = $this->putJson('admin/users/' . $user->id);
        $response->assertStatus(403)
            ->assertJson([
                'message' => __('auth.permission_required'),
                'status_code' => 403,
            ]);
    }

    /**
     * @test
     */
    public function update_user_by_user_has_permission_update()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $newData = [
            'first_name' => 'TESTER',
            'last_name' => 'TESTER',
            'email' => 'test@gmail.com',
            'phone' => '+963994622354',
            'password' => '123456',
            'password_confirmation' => '123456',
        ];
        $userLogin = $this->getUserHasPermission(PermissionType::UPDATE_USER);
        $image = UploadedFile::fake()->image('test_image.jpg');
        Sanctum::actingAs($userLogin);
        $response = $this->putJson("admin/users/{$user->id}", array_merge($newData, ['image' => $image]));
        /** @var User $userUpdated */
        $userUpdated = User::find($user->id);
        $response->assertStatus(200)->assertJsonStructure(self::JSON_STRUCTURE);

        $this->assertSame('TESTER', $userUpdated->first_name);
        $this->assertSame('test@gmail.com', $userUpdated->email);
        $this->assertSame('+963994622354', $userUpdated->phone);
        //        Storage::disk('testing')->assertMissing(User::$ImagesDir . '/' . $oldImageName);
        //        Storage::disk('testing')->assertExists(User::$ImagesDir . '/' . $userUpdated->image_name);
        $this->saveResponseToFile($response, 'admin/users/update.json');
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
        $userLogin = $this->getUserHasPermission(PermissionType::UPDATE_USER);
        $tooLongFirstName = str_repeat('a', 256);
        $tooLongLastName = str_repeat('b', 256);
        $tooLongEmail = str_repeat('c', 256) . '@example.com';

        Sanctum::actingAs($userLogin);
        $response = $this->putJson('admin/users/' . $user->id, [
                'first_name' => $tooLongFirstName,
                'last_name' => $tooLongLastName,
                'email' => $tooLongEmail,
                'country_id' => $country->id,
            ]);

        $response->assertStatus(422);
    }
}
