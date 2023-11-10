<?php

namespace Tests\API\V1\Controllers\Auth;

use App\Models\User;
use Tests\API\V1\V1TestCase;
use Tzsk\Otp\Facades\Otp;

class VerifyPhoneOtpTest extends V1TestCase
{
    /**
     * @test
     */
    public function user_can_verify_phone_using_otp_code_successfully()
    {
        $userData = User::factory()->create([
            'phone' => '+971585422379',
        ]);

        $key = $userData->phone . config('test.key');
        $code = Otp::generate($key);

        $response = $this->actingAs($userData)->postJson('auth/verify-otp', ['otp_code' => $code]);
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Phone verified successfully',
            ]);

        $this->assertNotNull($userData->fresh()->phone_verified_at);

        $this->saveResponseToFile($response, 'auth/verify_phone.json');

    }

    /**
     * @test
     */
    public function user_can_not_verify_phone_using_wrong_otp_code()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create([
            'phone' => '+971585422379',
        ]);

        $key = $user->phone . config('app.key');
        $code = Otp::generate($key);
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $response = $this->actingAs($user)->postJson('auth/verify-otp', ['otp_code' => '45768']);
        $response->assertStatus(422)
            ->assertJson(['data' => [
                'message' => ['Invalid OTP code'],
                ]]);
    }
}
