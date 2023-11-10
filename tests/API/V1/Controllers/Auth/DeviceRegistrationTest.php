<?php

namespace Tests\API\V1\Controllers\Auth;

use App\Http\API\V1\Repositories\Auth\AuthRepository;
use App\Models\Device;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\API\V1\V1TestCase;

class DeviceRegistrationTest extends V1TestCase
{
    use RefreshDatabase;

    /** @var AuthRepository */
    protected $authRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->authRepository = app(AuthRepository::class);
    }

    /**
     * @test
     */
    public function it_registers_a_device_for_authenticated_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $data = [
            'udid' => 'device-udid',
            'fcm_token' => '5987456',
        ];

        $response = $this->postJson('auth/register-device', $data);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Device is registered successfully',
            ]);

        $this->assertDatabaseHas('devices', [
            'udid' => $data['udid'],
            'fcm_token' => $data['fcm_token'],
            'user_id' => $user->id,
        ]);

        $this->saveResponseToFile($response, 'auth/register_device.json');
    }

    /**
     * @test
     */
    public function it_requires_fcm_token_if_udid_present()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('auth/register-device', ['udid' => '123456789']);

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
    public function it_can_retrieve_user_devices()
    {
        $user = User::factory()->create();

        $device1 = Device::factory()->create(['user_id' => $user->id]);
        $device2 = Device::factory()->create(['user_id' => $user->id]);

        $devices = $user->devices;

        $this->assertTrue($devices->contains($device1));
        $this->assertTrue($devices->contains($device2));
    }

    /**
     * @test
     */
    public function it_can_unregister_a_device()
    {
        $user = User::factory()->create();

        $device1 = Device::factory()->create(['user_id' => $user->id]);
        $device2 = Device::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);
        $this->authRepository->unregisterDevice($device1->udid);

        $this->assertSoftDeleted('devices', ['udid' => $device1->udid]);

        $this->assertDatabaseHas('devices', ['udid' => $device2->udid]);
    }
}
