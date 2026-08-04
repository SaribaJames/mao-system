<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $programs = [
            ['name' => 'Rice Program', 'coordinator_name' => 'Engr. Arjun Camacho', 'status' => 'active'],
            ['name' => 'Corn Program', 'coordinator_name' => 'Brian Albert Barba', 'status' => 'active'],
            ['name' => '4-H Club Program', 'coordinator_name' => 'Brian Albert Barba', 'status' => 'active'],
            ['name' => 'HVCDP-OAP-G3HP', 'coordinator_name' => 'Rowena O. Anyayahan', 'status' => 'active'],
            ['name' => 'NUPAP', 'coordinator_name' => 'Engr. Jaya Chaitanya Mendaza', 'status' => 'active'],
            ['name' => 'Livestock, Poultry & Fisheries', 'coordinator_name' => 'Gil Camases', 'status' => 'active'],
            ['name' => 'Swine Dispersal Project', 'coordinator_name' => 'Isidralyn O. Molit', 'status' => 'active'],
            ['name' => 'RBOs & RIC', 'coordinator_name' => 'Jeana Lyn Olaguera', 'status' => 'active'],
            ['name' => 'FITS Center (MAFC)', 'coordinator_name' => 'Elena Sales', 'status' => 'active'],
        ];

        foreach ($programs as $program) {
            DB::table('programs')->updateOrInsert(
                ['name' => $program['name']], // match on this column
                [
                    'coordinator_name' => $program['coordinator_name'],
                    'status' => $program['status'],
                    'updated_at' => $now,
                    'created_at' => $now, // only applied if inserting a new row
                ]
            );
        }
    }
}