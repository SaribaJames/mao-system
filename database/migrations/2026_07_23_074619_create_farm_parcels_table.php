<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_parcels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farmer_id')->constrained()->cascadeOnDelete();
            $table->integer('parcel_number')->default(1);
            $table->string('farm_location_barangay')->nullable();
            $table->string('farm_location_municipality')->nullable();
            $table->decimal('total_farm_area_ha', 10, 4)->nullable();
            $table->boolean('within_ancestral_domain')->default(false);
            $table->boolean('agrarian_reform_beneficiary')->default(false);
            $table->string('ownership_document_code')->nullable();
            $table->enum('ownership_type', ['registered_owner', 'tenant', 'lessee', 'others'])->nullable();
            $table->string('owner_name')->nullable();
            $table->string('crop_commodity')->nullable();
            $table->decimal('size_ha', 10, 4)->nullable();
            $table->integer('no_of_head')->nullable();
            $table->integer('farm_type')->nullable();
            $table->boolean('organic_practitioner')->default(false);
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_parcels');
    }
};