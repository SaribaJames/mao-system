<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Corrective migration. Laravel only tracks migration FILENAMES as
     * "already run" — rewriting the contents of 2026_08_18_000003 /
     * 2026_08_18_000004 after they'd already been applied had no effect on
     * the real database, so this table is still stuck on an early draft of
     * the schema. This patches it into the final shape using hasColumn()
     * checks, since we can't be sure exactly which draft it's currently on.
     */
    public function up(): void
    {
        Schema::table('distribution_lists', function (Blueprint $table) {
            if (!Schema::hasColumn('distribution_lists', 'program_label')) {
                $table->string('program_label')->nullable()->after('program_id');
            }
            if (!Schema::hasColumn('distribution_lists', 'stock_ids')) {
                $table->json('stock_ids')->nullable()->after('program_label');
            }
        });

        // Drop leftover columns from the very first draft (tied to Program
        // Activities), if they're still there.
        if (Schema::hasColumn('distribution_lists', 'item_columns')) {
            Schema::table('distribution_lists', function (Blueprint $table) {
                $table->dropColumn('item_columns');
            });
        }
        if (Schema::hasColumn('distribution_lists', 'program_activity_id')) {
            Schema::table('distribution_lists', function (Blueprint $table) {
                $table->dropForeign(['program_activity_id']);
                $table->dropColumn('program_activity_id');
            });
        }

        Schema::table('distribution_list_entries', function (Blueprint $table) {
            if (!Schema::hasColumn('distribution_list_entries', 'quantities')) {
                $table->json('quantities')->nullable();
            }
            if (!Schema::hasColumn('distribution_list_entries', 'transaction_ids')) {
                $table->json('transaction_ids')->nullable()->after('quantities');
            }
        });
    }

    public function down(): void
    {
        // Not reversible in a meaningful way — this only patches an
        // already-inconsistent table into the correct shape.
    }
};
