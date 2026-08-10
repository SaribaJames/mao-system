<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $fillable = [
        'name',
        'coordinator_name',
        'description',
        'status',
        'assigned_user_id',
    ];

    public function enrollments()
    {
        return $this->hasMany(ProgramEnrollment::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function isManagedBy(?User $user): bool
    {
        return $user && $this->assigned_user_id === $user->id;
    }

    public function farmers()
    {
        return $this->belongsToMany(Farmer::class, 'program_enrollments')
            ->withPivot(['status', 'enrollment_date', 'remarks'])
            ->withTimestamps();
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'active' => 'bg-green-100 text-green-700 border-green-200',
            'inactive' => 'bg-gray-100 text-gray-600 border-gray-200',
            default => 'bg-gray-100 text-gray-600 border-gray-200',
        };
    }

    public function activities()
    {
        return $this->hasMany(ProgramActivity::class);
    }

    
}
