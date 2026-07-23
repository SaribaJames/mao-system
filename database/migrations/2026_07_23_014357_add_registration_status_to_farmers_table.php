<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('farmers', function (Blueprint $table) {
            $table->enum('registration_status', ['pending', 'approved', 'rejected'])
                  ->default('pending')
                  ->after('status');
            $table->text('registration_rejection_reason')->nullable()->after('registration_status');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete()->after('registration_rejection_reason');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
        });
    }

    public function down(): void
    {
        Schema::table('farmers', function (Blueprint $table) {
            $table->dropColumn(['registration_status', 'registration_rejection_reason', 'approved_by', 'approved_at']);
        });
    }
};