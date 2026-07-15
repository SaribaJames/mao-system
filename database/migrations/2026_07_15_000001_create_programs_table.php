<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('coordinator_name');
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        $now = now();
        DB::table('programs')->insert([
            ['name' => 'Rice Program', 'coordinator_name' => 'Engr. Arjun Camacho', 'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Corn Program', 'coordinator_name' => 'Brian Albert Barba', 'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['name' => '4-H Club Program', 'coordinator_name' => 'Brian Albert Barba', 'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'HVCDP-OAP-G3HP', 'coordinator_name' => 'Rowena O. Anyayahan', 'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'NUPAP', 'coordinator_name' => 'Engr. Jaya Chaitanya Mendaza', 'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Livestock, Poultry & Fisheries', 'coordinator_name' => 'Gil Camases', 'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Swine Dispersal Project', 'coordinator_name' => 'Isidralyn O. Molit', 'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'RBOs & RIC', 'coordinator_name' => 'Jeana Lyn Olaguera', 'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'FITS Center (MAFC)', 'coordinator_name' => 'Elena Sales', 'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};