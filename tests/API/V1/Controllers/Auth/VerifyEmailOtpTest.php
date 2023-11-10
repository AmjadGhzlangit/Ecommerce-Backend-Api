<?php

namespace Tests\API\V1\Controllers\Auth;

use App\Models\User;
use Tests\API\V1\V1TestCase;
use Tzsk\Otp\Facades\Otp;

class VerifyEmailOtpTest extends V1TestCase
{
    /**
     * @test
     */
    public function user_can_verify_email_using_otp_code_successfully()
    {
        $userData = User::factory()->create([
            'email' => 'johndoe@example.com',
            'email_verified_at' => null,
        ]);

        $key = $userData->email . config('test.key');
        $code = Otp::generate($key);

        $response = $this->actingAs($userData)->postJson('auth/verify-otp/email', ['otp_code' => $code]);
        $this->withExceptionHandling();
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Email verified successfully',
            ]);
        $this->assertNotNull($userData->fresh()->email_verified_at);

        $this->saveResponseToFile($response, 'auth/verify_email.json');

    }

    /**
     * @test
     */
    public function user_can_not_verify_email_using_wrong_otp_code()
    {
        $this->withoutExceptionHandling();

        $userData = User::factory()->create([
            'email' => 'johndoe@example.com',
            'email_verified_at' => null,
        ]);

        $key = $userData->email . config('test.key');
        $code = Otp::generate($key);
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $response = $this->actingAs($userData)->postJson('auth/verify-otp/email', ['otp_code' => '45786']);
        $this->withExceptionHandling();
        $response->assertStatus(422)
            ->assertJson(['data' => [
                'message' => ['Invalid OTP code'],
                ]]);

        $this->assertNull($userData->fresh()->email_verified_at);

    }
}
