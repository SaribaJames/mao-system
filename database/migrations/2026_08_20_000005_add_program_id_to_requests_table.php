<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Lets a service request be tagged to the program it falls under (e.g. a
 * request for rice seed belongs to the Rice Program), so the coordinator who
 * runs that program can see and act on it instead of it sitting in a generic
 * queue. Nullable — requests like certifications belong to no program.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('requests', 'program_id')) {
            Schema::table('requests', function (Blueprint $table) {
                $table->foreignId('program_id')->nullable()->after('farmer_id')
                      ->constrained('programs')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('requests', 'program_id')) {
            Schema::table('requests', function (Blueprint $table) {
                $table->dropConstrainedForeignId('program_id');
            });
        }
    }
};
