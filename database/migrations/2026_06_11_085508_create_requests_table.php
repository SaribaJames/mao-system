<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique();
            $table->foreignId('farmer_id')->constrained('farmers')->cascadeOnDelete();
            $table->enum('request_type', [
                'seeds_distribution',
                'fertilizer_request',
                'pesticide_request',
                'equipment_request',
                'training_seminar',
                'technical_assistance',
                'financial_assistance',
                'others'
            ]);
            $table->foreignId('stock_id')->nullable()->constrained('stocks')->nullOnDelete();
            $table->string('item_service')->nullable();
            $table->decimal('quantity', 10, 2)->nullable();
            $table->string('quantity_unit')->nullable();
            $table->text('purpose')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
            $table->text('remarks')->nullable();
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};