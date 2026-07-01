<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransaction extends Model
{
    protected $fillable = [
        'stock_id', 'type', 'quantity',
        'recipient', 'farmer_id', 'notes', 'processed_by',
    ];

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    public function farmer()
    {
        return $this->belongsTo(Farmer::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}