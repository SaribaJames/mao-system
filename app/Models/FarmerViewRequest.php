<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A Staff/Brgy Rep account's request to see one farmer's full profile.
 * Admin approves or denies it; an approval only holds for 10 minutes
 * (expires_at) before the requester is locked out of the full profile
 * again and would need to request access to that farmer once more.
 */
class FarmerViewRequest extends Model
{
    protected $fillable = [
        'farmer_id',
        'user_id',
        'status',
        'decided_by',
        'decided_at',
        'expires_at',
    ];

    protected $casts = [
        'decided_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function farmer()
    {
        return $this->belongsTo(Farmer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function decidedBy()
    {
        return $this->belongsTo(User::class, 'decided_by');
    }

    public function isActive(): bool
    {
        return $this->status === 'approved'
            && $this->expires_at
            && $this->expires_at->isFuture();
    }
}
