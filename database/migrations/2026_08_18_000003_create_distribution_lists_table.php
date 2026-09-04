<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // A "Group Release" — the digital version of MAO's paper "Utilization
        // Form" / "Utilization Acknowledgement Receipt" used when releasing
        // seedlings, fertilizer, etc. to a group of farmers at once. Lives
        // under Stocks (not tied to a specific Program or Activity) so it's
        // open to any staff member and admin. `program_label` is just a
        // free-text tag (e.g. "Rice Program") for reporting — not a hard
        // link, since a release doesn't have to belong to a formal program.
        // `stock_ids` holds the ordered list of stock items included, so the
        // form can have one quantity column (fertilizer) or several
        // (assorted vegetable seeds) just like the paper versions.
        Schema::create('distribution_lists', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            // Optional formal link to a Program — when set, this release's
            // totals appear on that program's printed report. `program_label`
            // stays as a free-text fallback for releases with no formal
            // program set up, so picking one is never required.
            $table->foreignId('program_id')->nullable()->constrained('programs')->nullOnDelete();
            $table->string('program_label')->nullable();
            $table->json('stock_ids');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('distribution_lists');
    }
};
