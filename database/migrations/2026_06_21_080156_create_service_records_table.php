<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_records', function (Blueprint $table) {
            $table->id();
            $table->string('service_number')->unique();
            $table->foreignId('farmer_id')->constrained('farmers')->cascadeOnDelete();
            $table->enum('service_type', [
                'seed_distribution',
                'fertilizer_distribution',
                'pesticide_distribution',
                'equipment_assistance',
                'technical_assistance',
                'training_seminar',
                'farm_visit',
                'crop_insurance_assistance',
                'financial_assistance',
                'others'
            ]);
            $table->text('description')->nullable();
            $table->string('items_provided')->nullable();
            $table->decimal('quantity', 10, 2)->nullable();
            $table->string('quantity_unit')->nullable();
            $table->foreignId('stock_id')->nullable()->constrained('stocks')->nullOnDelete();
            $table->enum('status', ['completed', 'ongoing', 'cancelled'])->default('completed');
            $table->text('remarks')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('service_date')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_records');
    }
};