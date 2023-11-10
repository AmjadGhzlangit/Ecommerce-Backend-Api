<?php

namespace Database\Seeders;

use App\Enums\PermissionType;
use App\Enums\RoleType;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $allPermissions = Permission::all();

        $this->createRole(RoleType::SUPER_ADMIN->value, PermissionType::getDescription(RoleType::SUPER_ADMIN->value))
            ->givePermissionTo($allPermissions);
    }

    private function createRole($name, $description): Builder|Model
    {
        if (Role::whereName($name)->count() > 0) {
            return Role::whereName($name)->first();
        }

        return Role::create([
            'name' => $name,
            'description' => $description,
            'guard_name' => 'api',
        ]);
    }
}
