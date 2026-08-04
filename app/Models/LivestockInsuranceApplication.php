<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LivestockInsuranceApplication extends Model
{
    protected $fillable = [
        'farmer_id', 'cover_type', 'is_indigenous', 'tribe', 'is_pwd',
        'name_of_spouse', 'address', 'farm_address', 'contact_number',
        'animal_type', 'animal_type_other', 'purpose', 'animals',
        'total_heads', 'source_of_stock', 'no_of_housing_units',
        'birds_per_housing_unit', 'date_of_purchase',
        'sum_insured_per_head', 'total_sum_insured',
        'epidemic_coverage_1', 'epidemic_coverage_2', 'epidemic_coverage_3',
        'assignee_name', 'assignee_address', 'assignee_contact',
        'application_date', 'name_of_proponent', 'created_by',
    ];

    protected $casts = [
        'is_indigenous' => 'boolean',
        'is_pwd' => 'boolean',
        'animals' => 'array',
        'date_of_purchase' => 'date',
        'application_date' => 'date',
    ];

    public function farmer()
    {
        return $this->belongsTo(Farmer::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}