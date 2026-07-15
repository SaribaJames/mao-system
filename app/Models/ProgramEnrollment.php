<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramEnrollment extends Model
{
    protected $fillable = [
        'program_id', 'farmer_id', 'status',
        'enrollment_date', 'remarks', 'processed_by',
    ];

    protected $casts = [
        'enrollment_date' => 'date',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function farmer()
    {
        return $this->belongsTo(Farmer::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'active'    => 'bg-blue-100 text-blue-700',
            'completed' => 'bg-green-100 text-green-700',
            'dropped'   => 'bg-red-100 text-red-700',
            default     => 'bg-gray-100 text-gray-600',
        };
    }
}
