<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * The Stocks-level "Group Release" feature was replaced by per-activity
 * recipient lists inside Programs, so a coordinator's accomplishment record
 * and the stock they released stay together. These two tables are no longer
 * referenced by any model or controller.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('distribution_list_entries');
        Schema::dropIfExists('distribution_lists');
    }

    public function down(): void
    {
        // Intentionally empty — the feature these tables backed has been
        // removed, so there is nothing to restore them for.
    }
};
