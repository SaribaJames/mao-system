<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FarmerRequest extends Model
{
    protected $table = 'requests';

    protected $fillable = [
        'request_number', 'farmer_id', 'program_id', 'request_type',
        'stock_id', 'stock_transaction_id', 'released_quantity',
        'item_service', 'quantity', 'quantity_unit',
        'purpose', 'status', 'remarks',
        'submitted_by', 'processed_by', 'processed_at',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    public function farmer()
    {
        return $this->belongsTo(Farmer::class);
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    /** The program this request falls under, if any. */
    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    /** The actual stock release recorded when this request was completed. */
    public function stockTransaction()
    {
        return $this->belongsTo(StockTransaction::class, 'stock_transaction_id');
    }

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function getRequestTypeLabelAttribute()
    {
        $labels = [
            'seeds_distribution'   => 'Seeds Distribution',
            'fertilizer_request'   => 'Fertilizer Request',
            'pesticide_request'    => 'Pesticide Request',
            'equipment_request'    => 'Equipment Request',
            'training_seminar'     => 'Training/Seminar',
            'technical_assistance' => 'Technical Assistance',
            'financial_assistance' => 'Financial Assistance',
            'others'               => 'Others',
        ];
        return $labels[$this->request_type] ?? $this->request_type;
    }
}