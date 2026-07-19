<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coconut_farm_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farmer_id')->unique()->constrained()->cascadeOnDelete();

            // Land-Holding Status (coconut-specific, separate from Farmer's general one)
            $table->enum('coconut_land_holding_status', [
                'owner', 'owner_tiller', 'grower', 'tenant', 'tenant_worker', 'worker_laborer', 'others'
            ])->nullable();
            $table->string('parcel_no')->nullable();
            $table->string('coconut_farm_location_province')->nullable();
            $table->string('coconut_farm_location_municipality')->nullable();
            $table->string('coconut_farm_location_barangay')->nullable();

            // Land Ownership Area (in Hectares)
            $table->decimal('land_area_absolute', 8, 4)->nullable();
            $table->decimal('land_area_coconut', 8, 4)->nullable();
            $table->decimal('land_area_intercrop', 8, 4)->nullable();
            $table->decimal('land_area_other_crop', 8, 4)->nullable();
            $table->decimal('land_area_idle', 8, 4)->nullable();

            $table->enum('area_classification', [
                'inland_upland', 'inland_flat', 'coastal_upland', 'coastal_flat'
            ])->nullable();

            $table->boolean('organic_certified')->default(false);
            $table->boolean('gap_certified')->default(false);

            // Types of Processing Facilities
            $table->boolean('processing_dryer')->default(false);
            $table->string('processing_dryer_specify')->nullable();
            $table->boolean('processing_charcoal_kiln')->default(false);
            $table->boolean('processing_decort_machine')->default(false);
            $table->boolean('processing_others')->default(false);
            $table->string('processing_others_specify')->nullable();

            $table->decimal('distance_to_market_km', 8, 2)->nullable();
            $table->enum('coco_harvesting_cycle', [
                'less_than_45_days', '45_days', '60_days', '90_days', 'others'
            ])->nullable();
            $table->string('coco_harvesting_cycle_others_specify')->nullable();

            // Percent Utilization of Coconut Parts (stores the numeric code(s) from the CHOICES legend)
            $table->string('husks_utilization_codes')->nullable();
            $table->string('shell_utilization_codes')->nullable();
            $table->string('water_utilization_codes')->nullable();

            // Whom and Where Products are Sold (codes from CHOICES legend)
            $table->string('sold_to_whom_codes')->nullable();
            $table->string('sold_where_codes')->nullable();

            // Ownership Document (each checkbox from the form)
            $table->boolean('doc_certificate_land_transfer')->default(false);
            $table->boolean('doc_emancipation_patent')->default(false);
            $table->boolean('doc_individual_cloa')->default(false);
            $table->boolean('doc_collective_cloa')->default(false);
            $table->boolean('doc_co_ownership_cloa')->default(false);
            $table->boolean('doc_agricultural_sales_patent')->default(false);
            $table->boolean('doc_homestead_patent')->default(false);
            $table->boolean('doc_free_patent')->default(false);
            $table->boolean('doc_extrajudicial_partition')->default(false);
            $table->boolean('doc_certificate_of_title')->default(false);
            $table->boolean('doc_ancestral_domain_title')->default(false);
            $table->boolean('doc_ancestral_land_title')->default(false);
            $table->boolean('doc_tax_declaration')->default(false);
            $table->boolean('doc_deed_of_sale')->default(false);
            $table->boolean('doc_dar_id')->default(false);

            // For Tenant / Tenant-Worker / Farm Worker-Laborer — the owner/tenant they work for
            $table->string('owner_or_tenant_lastname')->nullable();
            $table->string('owner_or_tenant_firstname')->nullable();
            $table->string('owner_or_tenant_middlename')->nullable();
            $table->string('owner_or_tenant_extension')->nullable();
            $table->string('worker_farm_location_province')->nullable();
            $table->string('worker_farm_location_municipality')->nullable();
            $table->string('worker_farm_location_barangay')->nullable();

            // For Tenant-Worker / Farm Worker-Laborer / Others
            $table->string('kind_of_work_codes')->nullable();
            $table->decimal('monthly_income', 10, 2)->nullable();
            $table->integer('days_working_per_week')->nullable();
            $table->integer('days_working_per_month')->nullable();
            $table->integer('days_working_per_year')->nullable();

            // Signatures / verification
            $table->string('interviewed_by')->nullable();
            $table->string('encoded_by')->nullable();
            $table->date('date_applied')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coconut_farm_profiles');
    }
};