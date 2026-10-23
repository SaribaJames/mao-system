<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->string('item_name');
            $table->enum('category', [
                'seeds', 'fertilizer', 'pesticide',
                'equipment', 'tools', 'others'
            ]);
            $table->string('unit')->default('kg');
            $table->decimal('total_stock', 10, 2)->default(0);
            $table->decimal('released_stock', 10, 2)->default(0);
            $table->decimal('remaining_stock', 10, 2)->default(0);
            $table->enum('status', ['available', 'medium', 'low', 'out_of_stock'])->default('available');
            $table->text('description')->nullable();
            $table->foreignId('added_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('stock_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_id')->constrained('stocks')->cascadeOnDelete();
            $table->enum('type', ['add', 'release']);
            $table->decimal('quantity', 10, 2);
            $table->string('recipient')->nullable();
            $table->foreignId('farmer_id')->nullable()->constrained('farmers')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transactions');
        Schema::dropIfExists('stocks');
    }
};