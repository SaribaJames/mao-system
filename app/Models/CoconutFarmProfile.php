<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoconutFarmProfile extends Model
{
    protected $fillable = [
        'farmer_id',
        'coconut_land_holding_status', 'parcel_no',
        'coconut_farm_location_province', 'coconut_farm_location_municipality', 'coconut_farm_location_barangay',
        'land_area_absolute', 'land_area_coconut', 'land_area_intercrop', 'land_area_other_crop', 'land_area_idle',
        'area_classification',
        'organic_certified', 'gap_certified',
        'processing_dryer', 'processing_dryer_specify',
        'processing_charcoal_kiln', 'processing_decort_machine',
        'processing_others', 'processing_others_specify',
        'distance_to_market_km',
        'coco_harvesting_cycle', 'coco_harvesting_cycle_others_specify',
        'husks_utilization_codes', 'shell_utilization_codes', 'water_utilization_codes',
        'sold_to_whom_codes', 'sold_where_codes',
        'doc_certificate_land_transfer', 'doc_emancipation_patent', 'doc_individual_cloa',
        'doc_collective_cloa', 'doc_co_ownership_cloa', 'doc_agricultural_sales_patent',
        'doc_homestead_patent', 'doc_free_patent', 'doc_extrajudicial_partition',
        'doc_certificate_of_title', 'doc_ancestral_domain_title', 'doc_ancestral_land_title',
        'doc_tax_declaration', 'doc_deed_of_sale', 'doc_dar_id',
        'owner_or_tenant_lastname', 'owner_or_tenant_firstname', 'owner_or_tenant_middlename', 'owner_or_tenant_extension',
        'worker_farm_location_province', 'worker_farm_location_municipality', 'worker_farm_location_barangay',
        'kind_of_work_codes', 'monthly_income',
        'days_working_per_week', 'days_working_per_month', 'days_working_per_year',
        'interviewed_by', 'encoded_by', 'date_applied',
    ];

    protected $casts = [
        'organic_certified' => 'boolean',
        'gap_certified' => 'boolean',
        'processing_dryer' => 'boolean',
        'processing_charcoal_kiln' => 'boolean',
        'processing_decort_machine' => 'boolean',
        'processing_others' => 'boolean',
        'doc_certificate_land_transfer' => 'boolean',
        'doc_emancipation_patent' => 'boolean',
        'doc_individual_cloa' => 'boolean',
        'doc_collective_cloa' => 'boolean',
        'doc_co_ownership_cloa' => 'boolean',
        'doc_agricultural_sales_patent' => 'boolean',
        'doc_homestead_patent' => 'boolean',
        'doc_free_patent' => 'boolean',
        'doc_extrajudicial_partition' => 'boolean',
        'doc_certificate_of_title' => 'boolean',
        'doc_ancestral_domain_title' => 'boolean',
        'doc_ancestral_land_title' => 'boolean',
        'doc_tax_declaration' => 'boolean',
        'doc_deed_of_sale' => 'boolean',
        'doc_dar_id' => 'boolean',
        'date_applied' => 'date',
    ];

    public function farmer()
    {
        return $this->belongsTo(Farmer::class);
    }

    public function coconutTreeRecords()
    {
        return $this->hasMany(CoconutTreeRecord::class);
    }

    public function farmIncomeRecords()
    {
        return $this->hasMany(FarmIncomeRecord::class);
    }
}