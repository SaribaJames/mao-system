<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramActivityStockUsage extends Model
{
    protected $fillable = ['program_activity_id', 'stock_id', 'quantity_used', 'stock_transaction_id'];

    public function activity()
    {
        return $this->belongsTo(ProgramActivity::class, 'program_activity_id');
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    public function transaction()
    {
        return $this->belongsTo(StockTransaction::class, 'stock_transaction_id');
    }
}