<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssociationAccount extends Model
{
    protected $fillable = [
        'user_id', 'association_name', 'contact_number', 'address',
        'approval_status', 'approved_by', 'rejection_reason', 'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
