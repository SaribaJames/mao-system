<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Optional link to an actual registered Farmer record, so entries
        // added by checking someone off the registered/enrolled farmer list
        // (instead of typing a walk-in's name by hand) are properly tied to
        // that farmer, not just a free-text name.
        Schema::table('distribution_list_entries', function (Blueprint $table) {
            if (!Schema::hasColumn('distribution_list_entries', 'farmer_id')) {
                $table->foreignId('farmer_id')->nullable()->after('distribution_list_id')->constrained('farmers')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('distribution_list_entries', function (Blueprint $table) {
            if (Schema::hasColumn('distribution_list_entries', 'farmer_id')) {
                $table->dropConstrainedForeignId('farmer_id');
            }
        });
    }
};
