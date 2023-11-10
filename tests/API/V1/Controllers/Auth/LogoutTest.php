<?php

namespace Tests\API\V1\Controllers\Auth;

use App\Models\Device;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\API\V1\V1TestCase;

class LogoutTest extends V1TestCase
{
    /**
     * @test
     */
    public function logout_user_success()
    {
        $user = User::factory()->create();
        $device = Device::factory()->create(['user_id' => $user->id]);
        $token = $user->createAuthToken()->plainTextToken;
        // $device = Device::factory()->create();
        Sanctum::actingAs($user);
        $response = $this->postJson('auth/logout', [
            'token' => $token,
            'udid' => $device->udid,
        ]);
        $response->assertStatus(200)
            ->assertJson(['message' => 'Successfully logged out', 'status_code' => 200]);
        $this->saveResponseToFile($response, 'auth/logout.json');
    }

    /**
     * @test
     */
    public function logout_without_authentication_fails()
    {
        $response = $this->postJson('auth/logout', [
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthorized', 'status_code' => 401]);
    }

    /**
     * @test
     */
    public function logout_without_udid_success()
    {
        $user = User::factory()->create();
        $token = $user->createAuthToken()->plainTextToken;
        Sanctum::actingAs($user);
        $response = $this->postJson('auth/logout', [
            'token' => $token,
        ]);
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Successfully logged out',
            ]);
    }

    /**
     * @test
     */
    public function logout_with_udid_success_and_delete_device()
    {
        $user = User::factory()->create();
        $device = Device::factory()->create(['user_id' => $user->id]);
        $token = $user->createAuthToken()->plainTextToken;
        Sanctum::actingAs($user);
        $response = $this->postJson('auth/logout', [
            'token' => $token,
            'udid' => $device->udid,
        ]);
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Successfully logged out',
            ]);
        $this->assertDatabaseMissing('devices', ['udid' => $device->uuid]);
    }
}
