<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('swine_dispersal_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->cascadeOnDelete();
            $table->foreignId('farmer_id')->constrained()->cascadeOnDelete();
            $table->integer('piglets_received')->default(0);
            $table->date('date_received')->nullable();
            $table->integer('piglets_returned')->default(0);
            $table->date('date_returned')->nullable();
            $table->enum('status', ['waitlisted', 'received', 'compliant'])->default('waitlisted');
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('swine_dispersal_records');
    }
};