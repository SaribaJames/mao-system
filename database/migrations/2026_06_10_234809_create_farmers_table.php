<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farmers', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique()->nullable();
            $table->enum('enrollment_type', ['new', 'updating'])->default('new');

            // Part I - Personal Information
            $table->string('surname');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('extension_name')->nullable();
            $table->enum('sex', ['male', 'female']);
            $table->date('date_of_birth');
            $table->string('place_of_birth')->nullable();
            $table->string('house_lot_number')->nullable();
            $table->string('street')->nullable();
            $table->foreignId('barangay_id')->nullable()->constrained('barangays')->nullOnDelete();
            $table->string('municipality')->nullable();
            $table->string('province')->nullable();
            $table->string('region')->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('landline_number')->nullable();
            $table->string('religion')->nullable();
            $table->enum('civil_status', ['single', 'married', 'widowed', 'separated'])->nullable();
            $table->string('spouse_name')->nullable();
            $table->string('mother_maiden_name')->nullable();
            $table->boolean('is_household_head')->default(false);
            $table->string('household_head_name')->nullable();
            $table->string('household_head_relationship')->nullable();
            $table->integer('household_members_count')->nullable();
            $table->integer('household_male_count')->nullable();
            $table->integer('household_female_count')->nullable();

            // Education
            $table->enum('highest_education', [
                'pre_school', 'elementary', 'high_school_non_k12',
                'junior_high_k12', 'senior_high_k12', 'vocational',
                'college', 'post_graduate', 'none'
            ])->nullable();

            // Special Classifications
            $table->boolean('is_pwd')->default(false);
            $table->boolean('is_4ps_beneficiary')->default(false);
            $table->boolean('is_indigenous')->default(false);
            $table->string('indigenous_group_name')->nullable();
            $table->boolean('has_government_id')->default(false);
            $table->string('government_id_type')->nullable();
            $table->string('government_id_number')->nullable();
            $table->boolean('is_farmers_association_member')->default(false);
            $table->string('farmers_association_name')->nullable();

            // Emergency Contact
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_number')->nullable();

            // Part II - Farm Profile
            $table->enum('main_livelihood', ['farmer', 'farmworker', 'fisherfolk', 'agri_youth'])->nullable();

            // For Farmers
            $table->boolean('farming_rice')->default(false);
            $table->boolean('farming_corn')->default(false);
            $table->boolean('farming_other_crops')->default(false);
            $table->string('farming_other_crops_specify')->nullable();
            $table->boolean('farming_livestock')->default(false);
            $table->string('farming_livestock_specify')->nullable();
            $table->boolean('farming_poultry')->default(false);
            $table->string('farming_poultry_specify')->nullable();

            // Land Holding
            $table->enum('land_holding_status', [
                'owner', 'owner_tiller', 'grower', 'tenant',
                'tenant_worker', 'worker_laborer', 'others'
            ])->nullable();
            $table->string('farm_location_province')->nullable();
            $table->string('farm_location_municipality')->nullable();
            $table->string('farm_location_barangay')->nullable();
            $table->decimal('land_area_hectares', 10, 4)->nullable();

            // For Farmworkers
            $table->boolean('farmwork_land_preparation')->default(false);
            $table->boolean('farmwork_planting')->default(false);
            $table->boolean('farmwork_cultivation')->default(false);
            $table->boolean('farmwork_harvesting')->default(false);
            $table->boolean('farmwork_others')->default(false);
            $table->string('farmwork_others_specify')->nullable();

            // Income
            $table->decimal('gross_annual_income_farming', 15, 2)->nullable();
            $table->decimal('gross_annual_income_non_farming', 15, 2)->nullable();

            // Photo
            $table->string('photo')->nullable();

            // Status
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->foreignId('registered_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farmers');
    }
};