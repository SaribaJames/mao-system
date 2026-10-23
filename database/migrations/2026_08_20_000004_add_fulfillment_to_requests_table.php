<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Links a completed request to the stock transaction that actually released
 * the goods, so "Completed" means the farmer really received something rather
 * than just a label someone clicked.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            if (!Schema::hasColumn('requests', 'stock_transaction_id')) {
                $table->foreignId('stock_transaction_id')->nullable()->after('stock_id')
                      ->constrained('stock_transactions')->nullOnDelete();
            }
            if (!Schema::hasColumn('requests', 'released_quantity')) {
                $table->decimal('released_quantity', 10, 2)->nullable()->after('stock_transaction_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            if (Schema::hasColumn('requests', 'stock_transaction_id')) {
                $table->dropConstrainedForeignId('stock_transaction_id');
            }
            if (Schema::hasColumn('requests', 'released_quantity')) {
                $table->dropColumn('released_quantity');
            }
        });
    }
};
