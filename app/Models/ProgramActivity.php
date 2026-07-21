<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramActivity extends Model
{
    protected $fillable = [
        'program_id',
        'name',
        'performance_achieved',
        'achieved_value',
        'challenges_encountered',
        'proposed_intervention',
        'target_performance',
        'target_value',
        'value_unit',
        'expenditure_item',
        'budget_breakdown',
        'created_by',
    ];

    protected $casts = [
        'budget_breakdown' => 'array',
        'target_value' => 'decimal:2',
        'achieved_value' => 'decimal:2',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function stockUsages()
    {
        return $this->hasMany(ProgramActivityStockUsage::class);
    }

    public function totalBudget(): float
    {
        return array_sum($this->budget_breakdown ?? []);
    }
}