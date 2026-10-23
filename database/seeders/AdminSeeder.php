<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->first();

        User::firstOrCreate(
            ['email' => 'admin@mao-guinobatan.gov.ph'],
            [
                'name'     => 'MAO Administrator',
                'password' => bcrypt('Admin@1234'),
                'role_id'  => $adminRole->id,
                'status'   => 'active',
            ]
        );
    }
}