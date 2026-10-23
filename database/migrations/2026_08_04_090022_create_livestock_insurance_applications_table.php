<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('livestock_insurance_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farmer_id')->constrained()->cascadeOnDelete();

            $table->enum('cover_type', ['commercial', 'non_commercial', 'special']);
            $table->boolean('is_indigenous')->default(false);
            $table->string('tribe')->nullable();
            $table->boolean('is_pwd')->default(false);
            $table->string('name_of_spouse')->nullable();
            $table->string('address')->nullable();
            $table->string('farm_address')->nullable();
            $table->string('contact_number')->nullable();

            $table->enum('animal_type', ['cattle', 'carabao', 'swine', 'poultry', 'horse', 'goat', 'other']);
            $table->string('animal_type_other')->nullable();

            $table->enum('purpose', ['fattening', 'draft', 'broilers', 'pullets', 'breeding', 'dairy', 'layers', 'parent_stock']);

            // 6 rows x (male, female, age, breed, ear_mark, basic_color, proof_ownership)
            $table->json('animals')->nullable();

            $table->integer('total_heads')->nullable();
            $table->string('source_of_stock')->nullable();
            $table->integer('no_of_housing_units')->nullable();
            $table->integer('birds_per_housing_unit')->nullable();
            $table->date('date_of_purchase')->nullable();

            $table->decimal('sum_insured_per_head', 12, 2)->nullable();
            $table->decimal('total_sum_insured', 12, 2)->nullable();
            $table->string('epidemic_coverage_1')->nullable();
            $table->string('epidemic_coverage_2')->nullable();
            $table->string('epidemic_coverage_3')->nullable();

            $table->string('assignee_name')->nullable();
            $table->string('assignee_address')->nullable();
            $table->string('assignee_contact')->nullable();
            $table->date('application_date')->nullable();
            $table->string('name_of_proponent')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('livestock_insurance_applications');
    }
};