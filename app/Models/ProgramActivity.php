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
        'stock_ids',
        'budget_breakdown',
        'created_by',
    ];

    protected $casts = [
        'budget_breakdown' => 'array',
        'stock_ids' => 'array',
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

    /** Farmers who received resources during this activity. */
    public function recipients()
    {
        return $this->hasMany(ProgramActivityRecipient::class)->orderBy('id');
    }

    /**
     * The stock items this activity hands out, in the order the coordinator
     * picked them — these become the quantity columns on the printed list.
     */
    public function stockItems()
    {
        $ids = $this->stock_ids ?? [];

        if (empty($ids)) {
            return collect();
        }

        return Stock::whereIn('id', $ids)->get()
            ->sortBy(fn ($s) => array_search($s->id, $ids))
            ->values();
    }

    /** stock_id => total quantity handed out across every recipient. */
    public function distributedTotals(): array
    {
        $totals = [];

        foreach ($this->stockItems() as $stock) {
            $totals[$stock->id] = [
                'name' => $stock->item_name,
                'unit' => $stock->unit,
                'qty' => 0,
            ];
        }

        foreach ($this->recipients as $recipient) {
            foreach ($recipient->quantities ?? [] as $stockId => $qty) {
                if (isset($totals[$stockId])) {
                    $totals[$stockId]['qty'] += $qty;
                }
            }
        }

        return $totals;
    }

    public function totalBudget(): float
    {
        return array_sum($this->budget_breakdown ?? []);
    }
}