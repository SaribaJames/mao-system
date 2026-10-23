<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One row per participating farmer — matches the paper form's
        // NO / NAME / ADDRESS / (item quantities) / SIGNATURE columns.
        // Names are typed in manually (walk-ins and non-registered farmers
        // are common at these distributions), but barangay is a proper
        // foreign key so MAO gets an accurate per-barangay headcount for
        // planning how much stock to bring. `quantities` is keyed by
        // stock_id => qty; `transaction_ids` records the StockTransaction(s)
        // created for this farmer so removing an entry can cleanly reverse
        // the stock deduction and keep the audit trail intact.
        Schema::create('distribution_list_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('distribution_list_id')->constrained('distribution_lists')->cascadeOnDelete();
            $table->string('farmer_name');
            $table->foreignId('barangay_id')->nullable()->constrained('barangays')->nullOnDelete();
            $table->string('address')->nullable(); // purok/sitio detail, beyond just barangay
            $table->unsignedTinyInteger('age')->nullable();
            $table->enum('sex', ['M', 'F'])->nullable();
            $table->json('quantities')->nullable(); // keyed by stock_id => quantity
            $table->json('transaction_ids')->nullable(); // StockTransaction ids created for this entry
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('distribution_list_entries');
    }
};
