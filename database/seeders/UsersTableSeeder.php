<?php

namespace Database\Seeders;

use App\Enums\RoleType;
use App\Models\Country;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $country = Country::create(['name' => 'United Arab Emirates']);
        $root = $this->createUser(
            'super-admin',
            '+966555422373',
            $country->id,
            '67WkFDgf04eLVG6mWe5bgV89i4Q2',
        );
        $root->assignRole([RoleType::SUPER_ADMIN->value]);
    }

    private function createUser($name, $phone, $country, $firebase_uid): Model|User
    {
        return User::create([
            'first_name' => $name,
            'last_name' => $name,
            'email' => 'ddd@gmail.com',
            'password' => 's3Cr3t',
            'firebase_uid' => $firebase_uid,
            'phone' => $phone,
            'country_id' => $country,
            'email_verified_at' => now(),
        ]);

    }
}
