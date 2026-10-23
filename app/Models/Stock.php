<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $fillable = [
        'item_name', 'category', 'unit',
        'total_stock', 'released_stock', 'remaining_stock',
        'status', 'description', 'added_by',
    ];

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function transactions()
    {
        return $this->hasMany(StockTransaction::class);
    }

    public function updateStatus()
    {
        $percentage = $this->total_stock > 0
            ? ($this->remaining_stock / $this->total_stock) * 100
            : 0;

        if ($this->remaining_stock <= 0) {
            $this->status = 'out_of_stock';
        } elseif ($percentage <= 20) {
            $this->status = 'low';
        } elseif ($percentage <= 50) {
            $this->status = 'medium';
        } else {
            $this->status = 'available';
        }

        $this->save();
    }
}