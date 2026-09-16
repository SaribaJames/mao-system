<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Association accounts request resources for their organization, not
     * on behalf of one RSBSA-registered farmer, so a request no longer
     * always has a farmer attached.
     */
    public function up(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->foreignId('farmer_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->foreignId('farmer_id')->nullable(false)->change();
        });
    }
};
