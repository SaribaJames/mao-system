<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coconut_tree_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coconut_farm_profile_id')->constrained()->cascadeOnDelete();

            $table->string('variety_code')->nullable();      // CHOICES legend A: 1-Laguna Tall, 2-Tagnanan Tall, etc.
            $table->year('year_planted')->nullable();
            $table->string('planting_pattern_code')->nullable(); // CHOICES legend B
            $table->string('planting_distance_code')->nullable(); // CHOICES legend C
            $table->integer('no_of_trees')->nullable();
            $table->decimal('ave_nut_per_tree_year', 8, 2)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coconut_tree_records');
    }
};