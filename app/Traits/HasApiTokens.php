<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens as SanctumHasApiTokens;
use Laravel\Sanctum\NewAccessToken;

trait HasApiTokens
{
    use SanctumHasApiTokens;

    public function createToken(string $name, $expireMinutes = null, array $abilities = ['*']): NewAccessToken
    {
        $token = $this->tokens()
            ->create([
                'name' => $name,
                'token' => hash('sha256', $plainTextToken = Str::random(40)),
                'abilities' => $abilities,
                'expired_at' => $expireMinutes ? now()->addMinutes($expireMinutes) : null,
            ]);

        return new NewAccessToken($token, $plainTextToken);
    }

    public function createAuthToken(string $name = 'API', $expireMinutes = null, array $abilities = []): NewAccessToken
    {
        return $this->createToken($name, $expireMinutes ?? config('sanctum.auth_token_expiration'), array_merge($abilities, ['auth']));
    }

    public function createRefreshToken($name = 'API_REFRESH', $expireMinutes = null): NewAccessToken
    {
        return $this->createToken($name, $expireMinutes ?? config('sanctum.refresh_token_expiration'), ['refresh']);
    }
}
