<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('program_activities', function (Blueprint $table) {
            $table->decimal('target_value', 12, 2)->nullable()->after('target_performance');
            $table->decimal('achieved_value', 12, 2)->nullable()->after('performance_achieved');
            $table->string('value_unit')->nullable()->after('achieved_value');
        });
    }

    public function down(): void
    {
        Schema::table('program_activities', function (Blueprint $table) {
            $table->dropColumn(['target_value', 'achieved_value', 'value_unit']);
        });
    }
};