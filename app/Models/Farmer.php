<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Farmer extends Model
{
    protected $fillable = [
        'reference_number',
        'enrollment_type',
        'date_administered',
        'surname',
        'first_name',
        'middle_name',
        'extension_name',
        'sex',
        'date_of_birth',
        'place_of_birth',
        'house_lot_number',
        'street',
        'barangay_id',
        'municipality',
        'province',
        'region',
        'mobile_number',
        'landline_number',
        'religion',
        'civil_status',
        'spouse_name',
        'mother_maiden_name',
        'is_household_head',
        'household_head_name',
        'household_head_relationship',
        'household_members_count',
        'household_male_count',
        'household_female_count',
        'highest_education',
        'is_pwd',
        'is_4ps_beneficiary',
        'is_indigenous',
        'indigenous_group_name',
        'has_government_id',
        'government_id_type',
        'government_id_number',
        'is_farmers_association_member',
        'farmers_association_name',
        'emergency_contact_name',
        'emergency_contact_number',
        'main_livelihood',
        'farming_rice',
        'farming_corn',
        'farming_other_crops',
        'farming_other_crops_specify',
        'farming_livestock',
        'farming_livestock_specify',
        'farming_poultry',
        'farming_poultry_specify',
        'land_holding_status',
        'farm_location_province',
        'farm_location_municipality',
        'farm_location_barangay',
        'land_area_hectares',
        'farmwork_land_preparation',
        'farmwork_planting',
        'farmwork_cultivation',
        'farmwork_harvesting',
        'farmwork_others',
        'farmwork_others_specify',
        'fishing_capture',
        'fishing_aquaculture',
        'fishing_processing',
        'fishing_vending',
        'fishing_gleaning',
        'fishing_others',
        'fishing_others_specify',
        'agri_youth_farming_household',
        'agri_youth_formal_course',
        'agri_youth_nonformal_course',
        'agri_youth_participated_program',
        'agri_youth_others',
        'agri_youth_others_specify',
        'gross_annual_income_farming',
        'gross_annual_income_non_farming',
        'photo',
        'status',
        'registered_by',
        'registration_status',
        'registration_rejection_reason',
        'approved_by',
        'approved_at',

    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'date_administered' => 'date',
        'is_household_head' => 'boolean',
        'is_pwd' => 'boolean',
        'is_4ps_beneficiary' => 'boolean',
        'is_indigenous' => 'boolean',
        'has_government_id' => 'boolean',
        'is_farmers_association_member' => 'boolean',
        'farming_rice' => 'boolean',
        'farming_corn' => 'boolean',
        'farming_other_crops' => 'boolean',
        'farming_livestock' => 'boolean',
        'farming_poultry' => 'boolean',
        'farmwork_land_preparation' => 'boolean',
        'farmwork_planting' => 'boolean',
        'farmwork_cultivation' => 'boolean',
        'farmwork_harvesting' => 'boolean',
        'farmwork_others' => 'boolean',
        'fishing_capture' => 'boolean',
        'fishing_aquaculture' => 'boolean',
        'fishing_processing' => 'boolean',
        'fishing_vending' => 'boolean',
        'fishing_gleaning' => 'boolean',
        'fishing_others' => 'boolean',
        'agri_youth_farming_household' => 'boolean',
        'agri_youth_formal_course' => 'boolean',
        'agri_youth_nonformal_course' => 'boolean',
        'agri_youth_participated_program' => 'boolean',
        'agri_youth_others' => 'boolean',
    ];

    public function barangay()
    {
        return $this->belongsTo(Barangay::class);
    }

    public function registeredBy()
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    public function coconutFarmProfile()
    {
        return $this->hasOne(CoconutFarmProfile::class);
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->middle_name} {$this->surname}";
    }

    /**
     * Safely resolve the Cloudinary URL for this farmer's photo.
     * Returns null instead of throwing if the asset can't be found or
     * Cloudinary can't be reached, so a missing/broken photo never
     * breaks printing or viewing the farmer's profile.
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if (!$this->photo) {
            return null;
        }
        try {
            return \Illuminate\Support\Facades\Storage::disk('cloudinary')->url($this->photo);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning(
                "Farmer #{$this->id}: could not resolve Cloudinary photo URL for '{$this->photo}': {$e->getMessage()}"
            );
            return null;
        }
    }

    /** Every program enrolment this farmer has, in any state. */
    public function enrollments()
    {
        return $this->hasMany(ProgramEnrollment::class);
    }

    /** Programs this farmer belongs to, with the enrolment state on the pivot. */
    public function programs()
    {
        return $this->belongsToMany(Program::class, 'program_enrollments')
            ->withPivot(['status', 'enrollment_date', 'remarks'])
            ->withTimestamps();
    }

    /**
     * Only the programs the farmer is CURRENTLY in — completed and dropped
     * enrolments are history, not current membership.
     */
    public function activePrograms()
    {
        return $this->programs()->wherePivot('status', 'active');
    }

    public function farmParcels()
    {
        return $this->hasMany(FarmParcel::class)->orderBy('parcel_number');
    }
}