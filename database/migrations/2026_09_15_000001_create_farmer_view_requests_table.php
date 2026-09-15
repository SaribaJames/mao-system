<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tracks a Staff/Brgy Rep account's request to view one farmer's full
     * profile. Admin/staff/brgy no longer see full farmer PII by default —
     * an admin must approve a request here, and the approval only lasts
     * 10 minutes (see expires_at) before the requester is locked out again.
     */
    public function up(): void
    {
        Schema::create('farmer_view_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farmer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['pending', 'approved', 'denied'])->default('pending');
            $table->foreignId('decided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('decided_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'farmer_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farmer_view_requests');
    }
};
