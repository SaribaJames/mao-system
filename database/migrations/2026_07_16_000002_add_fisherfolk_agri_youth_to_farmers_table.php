<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('farmers', function (Blueprint $table) {
            // Fisherfolk — Type of Fishing Activity
            $table->boolean('fishing_capture')->default(false)->after('farmwork_others_specify');
            $table->boolean('fishing_aquaculture')->default(false)->after('fishing_capture');
            $table->boolean('fishing_processing')->default(false)->after('fishing_aquaculture');
            $table->boolean('fishing_vending')->default(false)->after('fishing_processing');
            $table->boolean('fishing_gleaning')->default(false)->after('fishing_vending');
            $table->boolean('fishing_others')->default(false)->after('fishing_gleaning');
            $table->string('fishing_others_specify')->nullable()->after('fishing_others');

            // Agri Youth — Type of Involvement
            $table->boolean('agri_youth_farming_household')->default(false)->after('fishing_others_specify');
            $table->boolean('agri_youth_formal_course')->default(false)->after('agri_youth_farming_household');
            $table->boolean('agri_youth_nonformal_course')->default(false)->after('agri_youth_formal_course');
            $table->boolean('agri_youth_participated_program')->default(false)->after('agri_youth_nonformal_course');
            $table->boolean('agri_youth_others')->default(false)->after('agri_youth_participated_program');
            $table->string('agri_youth_others_specify')->nullable()->after('agri_youth_others');
        });
    }

    public function down(): void
    {
        Schema::table('farmers', function (Blueprint $table) {
            $table->dropColumn([
                'fishing_capture', 'fishing_aquaculture', 'fishing_processing',
                'fishing_vending', 'fishing_gleaning', 'fishing_others', 'fishing_others_specify',
                'agri_youth_farming_household', 'agri_youth_formal_course', 'agri_youth_nonformal_course',
                'agri_youth_participated_program', 'agri_youth_others', 'agri_youth_others_specify',
            ]);
        });
    }
};