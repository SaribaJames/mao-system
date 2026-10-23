<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Barangay;

class BarangaySeeder extends Seeder
{
    public function run(): void
    {
        $barangays = [
            'Agpay', 'Balite', 'Buyo', 'Calzada', 'Cuyaoyao',
            'Dominorog', 'Gotob', 'Inamnan Grande', 'Inamnan Pequeño',
            'Ligban', 'Malobago', 'Marilima', 'Mauraro', 'Minto',
            'Palanog', 'Palo', 'Pangpang', 'Payahan', 'Poblacion',
            'Quirangay', 'Quitago', 'San Francisco', 'Sinungtan', 'Tobgon',
        ];

        foreach ($barangays as $name) {
            Barangay::firstOrCreate(['name' => $name]);
        }
    }
}