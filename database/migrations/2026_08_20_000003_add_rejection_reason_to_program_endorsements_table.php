<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A rejected endorsement must say WHY, so the barangay representative knows
 * what was lacking and can decide whether to endorse the farmer again with a
 * stronger justification.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('program_endorsements', 'rejection_reason')) {
            Schema::table('program_endorsements', function (Blueprint $table) {
                $table->text('rejection_reason')->nullable()->after('notes');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('program_endorsements', 'rejection_reason')) {
            Schema::table('program_endorsements', function (Blueprint $table) {
                $table->dropColumn('rejection_reason');
            });
        }
    }
};
