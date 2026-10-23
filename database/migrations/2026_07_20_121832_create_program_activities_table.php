<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('performance_achieved')->nullable();
            $table->text('challenges_encountered')->nullable();
            $table->text('proposed_intervention')->nullable();
            $table->text('target_performance')->nullable();
            $table->string('expenditure_item')->nullable();
            $table->json('budget_breakdown')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_activities');
    }
};