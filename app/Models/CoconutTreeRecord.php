<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoconutTreeRecord extends Model
{
    protected $fillable = [
        'coconut_farm_profile_id',
        'variety_code', 'year_planted', 'planting_pattern_code',
        'planting_distance_code', 'no_of_trees', 'ave_nut_per_tree_year',
    ];

    public function coconutFarmProfile()
    {
        return $this->belongsTo(CoconutFarmProfile::class);
    }
}