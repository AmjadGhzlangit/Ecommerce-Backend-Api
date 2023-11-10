<?php

namespace Database\Factories;

use App\Models\Country;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Propaganistas\LaravelPhone\Exceptions\NumberParseException;
use Propaganistas\LaravelPhone\PhoneNumber;

/**
 * @extends Factory
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $image = UploadedFile::fake()->image('test_image.jpg');
        $imageHashName = $image->hashName();
        return [
            'first_name' => $this->faker->name(),
            'last_name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->validPhoneNumber('AE'),
            'country_id' => Country::factory()->create()->id,
            'image' => 'https://dev.alebdaa.net/storage/images/users/' . $imageHashName,
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
            'password' => 'secret',
            'remember_token' => Str::random(10),

        ];
    }

    public function validPhoneNumber($countryCode): string
    {
        $result = null;

        do {
            $fakePhone = $this->faker->phoneNumber;
            try {
                $object = new PhoneNumber($fakePhone, $countryCode);
                $object->formatE164();
                $result = $fakePhone;
            } catch (NumberParseException $e) {
            }
        } while (!$result);

        return $result;
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
