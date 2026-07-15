<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Program;

class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        $programs = [
            ['name' => 'Rice Program', 'coordinator_name' => 'Engr. Arjun Camacho'],
            ['name' => 'Corn Program', 'coordinator_name' => 'Brian Albert Barba'],
            ['name' => '4-H Club Program', 'coordinator_name' => 'Brian Albert Barba'],
            ['name' => 'HVCDP-OAP-G3HP', 'coordinator_name' => 'Rowena O. Anyayahan'],
            ['name' => 'NUPAP', 'coordinator_name' => 'Engr. Jaya Chaitanya Mendaza'],
            ['name' => 'Livestock, Poultry & Fisheries', 'coordinator_name' => 'Gil Camases'],
            ['name' => 'Swine Dispersal Project', 'coordinator_name' => 'Isidralyn O. Molit'],
            ['name' => 'RBOs & RIC', 'coordinator_name' => 'Jeana Lyn Olaguera'],
            ['name' => 'FITS Center (MAFC)', 'coordinator_name' => 'Elena Sales'],
        ];

        foreach ($programs as $program) {
            Program::firstOrCreate(
                ['name' => $program['name']],
                ['coordinator_name' => $program['coordinator_name'], 'status' => 'active']
            );
        }
    }
}
