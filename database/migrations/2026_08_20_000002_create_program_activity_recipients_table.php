<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Farmers who actually received resources during a program activity.
 * This is the coordinator's proof-of-distribution list: it prints out for
 * signatures and drives the "Resources Distributed" totals on the program
 * accomplishment report.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('program_activities', 'stock_ids')) {
            Schema::table('program_activities', function (Blueprint $table) {
                $table->json('stock_ids')->nullable()->after('expenditure_item');
            });
        }

        if (!Schema::hasTable('program_activity_recipients')) {
            Schema::create('program_activity_recipients', function (Blueprint $table) {
                $table->id();
                $table->foreignId('program_activity_id')->constrained()->cascadeOnDelete();
                $table->foreignId('farmer_id')->nullable()->constrained()->nullOnDelete();
                $table->string('farmer_name');
                $table->foreignId('barangay_id')->nullable()->constrained()->nullOnDelete();
                $table->string('address')->nullable();
                $table->unsignedSmallInteger('age')->nullable();
                $table->enum('sex', ['M', 'F'])->nullable();
                $table->json('quantities')->nullable();
                $table->json('transaction_ids')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('program_activity_recipients');

        if (Schema::hasColumn('program_activities', 'stock_ids')) {
            Schema::table('program_activities', function (Blueprint $table) {
                $table->dropColumn('stock_ids');
            });
        }
    }
};
