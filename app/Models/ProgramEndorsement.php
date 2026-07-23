<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramEndorsement extends Model
{
    protected $fillable = [
        'farmer_id', 'program_id', 'endorsed_by', 'status',
        'notes', 'reviewed_by', 'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function farmer()
    {
        return $this->belongsTo(Farmer::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function endorser()
    {
        return $this->belongsTo(\App\Models\User::class, 'endorsed_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(\App\Models\User::class, 'reviewed_by');
    }
}