<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FarmParcel extends Model
{
    protected $fillable = [
        'farmer_id', 'parcel_number', 'farm_location_barangay',
        'farm_location_municipality', 'total_farm_area_ha',
        'within_ancestral_domain', 'agrarian_reform_beneficiary',
        'ownership_document_code', 'ownership_type', 'owner_name',
        'crop_commodity', 'size_ha', 'no_of_head', 'farm_type',
        'organic_practitioner', 'remarks',
    ];

    protected $casts = [
        'within_ancestral_domain' => 'boolean',
        'agrarian_reform_beneficiary' => 'boolean',
        'organic_practitioner' => 'boolean',
    ];

    public function farmer()
    {
        return $this->belongsTo(Farmer::class);
    }
}