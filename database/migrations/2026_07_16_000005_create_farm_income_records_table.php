<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_income_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coconut_farm_profile_id')->constrained()->cascadeOnDelete();

            $table->string('income_type_code')->nullable(); // CHOICES legend D: Coconut Products, Food/Non-Food By-Products, Intercrops, Other Crops, Livestock/Poultry
            $table->decimal('quantity_per_hectare_year', 10, 2)->nullable();
            $table->enum('unit', ['kg', 'pc', 'liter', 'head', 'other'])->nullable();
            $table->string('unit_other_specify')->nullable();
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->string('expense_type_code')->nullable(); // CHOICES legend E: 1-Farm Labor, 2-Marketing, 3-Farm Inputs, 4-Others
            $table->decimal('expense_amount', 10, 2)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_income_records');
    }
};