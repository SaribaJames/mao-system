<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'admin',         'description' => 'MAO Administrator'],
            ['name' => 'staff',         'description' => 'Agricultural Technician'],
            ['name' => 'barangay_user', 'description' => 'Authorized Barangay Secretary'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['name' => $role['name']], $role);
        }
    }
}