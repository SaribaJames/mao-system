<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FarmIncomeRecord extends Model
{
    protected $fillable = [
        'coconut_farm_profile_id',
        'income_type_code', 'quantity_per_hectare_year', 'unit', 'unit_other_specify',
        'unit_price', 'expense_type_code', 'expense_amount',
    ];

    public function coconutFarmProfile()
    {
        return $this->belongsTo(CoconutFarmProfile::class);
    }
}