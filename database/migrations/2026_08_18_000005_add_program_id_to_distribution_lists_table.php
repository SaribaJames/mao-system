<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Guarded: on an existing database the column is missing and gets added
        // here; on a fresh `migrate:fresh` the create migration already made it,
        // so adding it again would fail with "Duplicate column name".
        if (!Schema::hasTable('distribution_lists')) {
            return;
        }

        if (!Schema::hasColumn('distribution_lists', 'program_id')) {
            Schema::table('distribution_lists', function (Blueprint $table) {
                $table->foreignId('program_id')->nullable()->after('title')->constrained('programs')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('distribution_lists') && Schema::hasColumn('distribution_lists', 'program_id')) {
            Schema::table('distribution_lists', function (Blueprint $table) {
                $table->dropConstrainedForeignId('program_id');
            });
        }
    }
};
