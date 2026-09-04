<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * One farmer on a program activity's distribution list — who received what,
 * and which stock transactions were created for them so the release can be
 * reversed cleanly if the row is removed.
 */
class ProgramActivityRecipient extends Model
{
    protected $fillable = [
        'program_activity_id',
        'farmer_id',
        'farmer_name',
        'barangay_id',
        'address',
        'age',
        'sex',
        'quantities',
        'transaction_ids',
    ];

    protected $casts = [
        'quantities' => 'array',
        'transaction_ids' => 'array',
    ];

    public function activity()
    {
        return $this->belongsTo(ProgramActivity::class, 'program_activity_id');
    }

    public function farmer()
    {
        return $this->belongsTo(Farmer::class);
    }

    public function barangay()
    {
        return $this->belongsTo(Barangay::class);
    }
}
