<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangayAccount extends Model
{
    protected $fillable = [
        'user_id', 'barangay_id', 'approval_status',
        'approved_by', 'rejection_reason', 'approved_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function barangay()
    {
        return $this->belongsTo(Barangay::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}