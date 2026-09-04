<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_transactions', function (Blueprint $table) {
            // Who the resources were received from (partner org, donor, government allocation, etc.)
            $table->string('partner_name')->nullable()->after('notes');
            // Delivery receipt / purchase order / donation reference number
            $table->string('reference_number')->nullable()->after('partner_name');
            // The date the resources were actually received (may differ from when it was logged)
            $table->date('received_date')->nullable()->after('reference_number');
            // Scanned/photographed proof of delivery (stored on Cloudinary, like everything else)
            $table->string('attachment_path')->nullable()->after('received_date');
        });
    }

    public function down(): void
    {
        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->dropColumn(['partner_name', 'reference_number', 'received_date', 'attachment_path']);
        });
    }
};
