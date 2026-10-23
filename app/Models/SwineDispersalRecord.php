<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SwineDispersalRecord extends Model
{
    protected $fillable = [
        'program_id', 'farmer_id', 'piglets_received', 'date_received',
        'piglets_returned', 'date_returned', 'status', 'notes', 'recorded_by',
    ];

    protected $casts = [
        'date_received' => 'date',
        'date_returned' => 'date',
    ];

    public function farmer()
    {
        return $this->belongsTo(Farmer::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function recorder()
    {
        return $this->belongsTo(\App\Models\User::class, 'recorded_by');
    }
}