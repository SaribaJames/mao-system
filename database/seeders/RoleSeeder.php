<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'superadmin',    'description' => 'System Administrator'],
            ['name' => 'admin',         'description' => 'MAO Staff'],
            ['name' => 'barangay_user', 'description' => 'Authorized Barangay Secretary'],
            ['name' => 'viewer',        'description' => 'Read-only Viewer'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role['name']], $role);
        }
    }
}