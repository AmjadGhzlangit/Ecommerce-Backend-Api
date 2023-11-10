<?php

namespace Database\Seeders;

use App\Enums\PermissionType;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (PermissionType::cases() as $value) {
            $this->createPermission($value->value, PermissionType::getDescription($value->value));
        }
    }

    private function createPermission($name, $description): void
    {
        if (Permission::whereName($name)->count() === 0) {
            echo "creating permission {$name} \n";

            Permission::create([
                'name' => $name,
                'description' => $description,
            ]);
        }
    }
}
