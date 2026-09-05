<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Farmer;
use App\Models\Barangay;
use App\Models\FarmParcel;
use App\Models\Stock;
use App\Models\StockTransaction;
use App\Models\User;

class DevDataSeeder extends Seeder
{
    public function run(): void
    {
        $barangays = Barangay::inRandomOrder()->limit(10)->get();

        if ($barangays->count() < 2) {
            $this->command->warn('Need at least 2 barangays seeded first. Skipping farmer seeding.');
        } else {
            $registeredBy = User::first()?->id;

            $firstNamesMale = ['Juan', 'Pedro', 'Jose', 'Ramon', 'Antonio', 'Ricardo', 'Eduardo', 'Manuel', 'Roberto', 'Ernesto', 'Carlos', 'Fernando', 'Alfredo', 'Danilo', 'Rodrigo'];
            $firstNamesFemale = ['Maria', 'Ana', 'Rosa', 'Elena', 'Teresita', 'Corazon', 'Josefina', 'Remedios', 'Luz', 'Carmen', 'Estrella', 'Norma', 'Erlinda', 'Marilou', 'Susana'];
            $surnames = ['Dela Cruz', 'Santos', 'Reyes', 'Bautista', 'Garcia', 'Mendoza', 'Torres', 'Flores', 'Ramos', 'Aquino', 'Villanueva', 'Castillo', 'Gonzales', 'Rivera', 'Aguilar', 'Salazar', 'Marasigan', 'Belmonte', 'Ortega', 'Navarro'];

            $livelihoods = ['farmer', 'farmworker', 'fisherfolk', 'agri_youth'];
            $landStatuses = ['owner', 'owner_tiller', 'grower', 'tenant', 'tenant_worker', 'worker_laborer', 'others'];
            $ownershipTypes = ['registered_owner', 'tenant', 'lessee', 'others'];
            $crops = ['Rice', 'Corn', 'Coconut', 'Vegetables', 'Abaca'];

            $totalFarmers = 0;

            foreach ($barangays as $barangay) {
                $farmerCount = rand(5, 10);

                for ($i = 0; $i < $farmerCount; $i++) {
                    $sex = rand(0, 1) ? 'male' : 'female';
                    $firstName = $sex === 'male'
                        ? $firstNamesMale[array_rand($firstNamesMale)]
                        : $firstNamesFemale[array_rand($firstNamesFemale)];
                    $surname = $surnames[array_rand($surnames)];

                    $farmer = Farmer::create([
                        'enrollment_type'    => 'new',
                        'surname'            => $surname,
                        'first_name'         => $firstName,
                        'sex'                => $sex,
                        'date_of_birth'      => now()->subYears(rand(25, 65))->subDays(rand(0, 365)),
                        'place_of_birth'     => 'Guinobatan, Albay',
                        'barangay_id'        => $barangay->id,
                        'municipality'       => 'Guinobatan',
                        'province'           => 'Albay',
                        'region'             => 'Region V (Bicol Region)',
                        'mobile_number'      => '09' . rand(100000000, 999999999),
                        'civil_status'       => ['single', 'married', 'widowed', 'separated'][rand(0, 3)],
                        'is_household_head'  => (bool) rand(0, 1),
                        'highest_education'  => 'high_school_non_k12',
                        'main_livelihood'    => $livelihoods[rand(0, 3)],
                        'farming_rice'       => (bool) rand(0, 1),
                        'farming_corn'       => (bool) rand(0, 1),
                        'land_holding_status'=> $landStatuses[rand(0, 6)],
                        'farm_location_province'     => 'Albay',
                        'farm_location_municipality' => 'Guinobatan',
                        'farm_location_barangay'     => $barangay->name,
                        'land_area_hectares' => round(rand(5, 50) / 10, 4),
                        'gross_annual_income_farming' => rand(20000, 150000),
                        'status'             => 'active',
                        'registration_status'=> 'approved',
                        'registered_by'      => $registeredBy,
                    ]);

                    $parcelCount = rand(1, 3);
                    for ($p = 1; $p <= $parcelCount; $p++) {
                        FarmParcel::create([
                            'farmer_id'                   => $farmer->id,
                            'parcel_number'                => $p,
                            'farm_location_barangay'       => $barangay->name,
                            'farm_location_municipality'   => 'Guinobatan',
                            'total_farm_area_ha'           => round(rand(5, 30) / 10, 4),
                            'within_ancestral_domain'       => (bool) rand(0, 1),
                            'agrarian_reform_beneficiary'   => (bool) rand(0, 1),
                            'ownership_document_code'       => 'OCT-' . rand(1000, 9999),
                            'ownership_type'                => $ownershipTypes[rand(0, 3)],
                            'owner_name'                    => "{$firstName} {$surname}",
                            'crop_commodity'                => $crops[rand(0, 4)],
                            'size_ha'                       => round(rand(5, 30) / 10, 4),
                            'no_of_head'                    => rand(0, 1) ? rand(1, 10) : null,
                            'farm_type'                     => rand(1, 2),
                            'organic_practitioner'          => (bool) rand(0, 1),
                            'remarks'                        => null,
                        ]);
                    }

                    $totalFarmers++;
                }
            }

            $this->command->info("Seeded {$totalFarmers} farmers across {$barangays->count()} barangays, with farm parcels.");
        }

        // Stocks
        $addedBy = User::first()?->id;

        $stockItems = [
            ['item_name' => 'Hybrid Rice Seeds', 'category' => 'seeds', 'unit' => 'kg', 'total' => 500],
            ['item_name' => 'Urea Fertilizer 46-0-0', 'category' => 'fertilizer', 'unit' => 'sack', 'total' => 200],
            ['item_name' => 'Corn Seeds', 'category' => 'seeds', 'unit' => 'kg', 'total' => 300],
            ['item_name' => 'Insecticide (Cypermethrin)', 'category' => 'pesticide', 'unit' => 'liter', 'total' => 80],
            ['item_name' => 'Hand Tractor', 'category' => 'equipment', 'unit' => 'unit', 'total' => 5],
            ['item_name' => 'Bolo/Farm Tools Set', 'category' => 'tools', 'unit' => 'set', 'total' => 40],
        ];

        foreach ($stockItems as $item) {
            $released = round($item['total'] * (rand(10, 40) / 100), 2);
            $remaining = $item['total'] - $released;

            $status = match (true) {
                $remaining <= 0 => 'out_of_stock',
                $remaining < $item['total'] * 0.25 => 'low',
                $remaining < $item['total'] * 0.6 => 'medium',
                default => 'available',
            };

            $stock = Stock::create([
                'item_name'       => $item['item_name'],
                'category'        => $item['category'],
                'unit'            => $item['unit'],
                'total_stock'     => $item['total'],
                'released_stock'  => $released,
                'remaining_stock' => $remaining,
                'status'          => $status,
                'description'     => null,
                'added_by'        => $addedBy,
            ]);

            StockTransaction::create([
                'stock_id'     => $stock->id,
                'type'         => 'add',
                'quantity'     => $item['total'],
                'recipient'    => null,
                'farmer_id'    => null,
                'notes'        => 'Initial stock intake',
                'processed_by' => $addedBy,
            ]);

            if ($released > 0) {
                $farmer = Farmer::inRandomOrder()->first();

                StockTransaction::create([
                    'stock_id'     => $stock->id,
                    'type'         => 'release',
                    'quantity'     => $released,
                    'recipient'    => $farmer ? "{$farmer->first_name} {$farmer->surname}" : 'Barangay Distribution',
                    'farmer_id'    => $farmer?->id,
                    'notes'        => 'Sample distribution',
                    'processed_by' => $addedBy,
                ]);
            }
        }

        $this->command->info('Seeded ' . count($stockItems) . ' stock items with transactions.');
    }
}