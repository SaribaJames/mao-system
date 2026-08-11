<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceRecord extends Model
{
    protected $fillable = [
        'service_number', 'farmer_id', 'service_type',
        'description', 'items_provided', 'quantity',
        'quantity_unit', 'stock_id', 'status',
        'remarks', 'processed_by', 'service_date',
    ];

    protected $casts = [
        'service_date' => 'datetime',
    ];

    public function farmer()
    {
        return $this->belongsTo(Farmer::class);
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function getServiceTypeLabelAttribute()
    {
        $labels = [
            'seed_distribution'        => 'Seed Distribution',
            'fertilizer_distribution'  => 'Fertilizer Distribution',
            'pesticide_distribution'   => 'Pesticide Distribution',
            'equipment_assistance'     => 'Equipment Assistance',
            'technical_assistance'     => 'Technical Assistance',
            'training_seminar'         => 'Training/Seminar',
            'farm_visit'               => 'Farm Visit',
            'crop_insurance_assistance'=> 'Crop Insurance Assistance',
            'financial_assistance'     => 'Financial Assistance',
            'others'                   => 'Others',
        ];
        return $labels[$this->service_type] ?? $this->service_type;
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'completed' => 'bg-green-100 text-green-700',
            'ongoing'   => 'bg-yellow-100 text-yellow-700',
            'cancelled' => 'bg-red-100 text-red-700',
            default     => 'bg-gray-100 text-gray-600',
        };
    }
}