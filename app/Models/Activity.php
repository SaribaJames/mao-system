<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = [
        'type', 'title', 'content',
        'priority', 'event_date', 'location',
        'status', 'created_by',
    ];

    protected $casts = [
        'event_date' => 'date',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            'high'   => 'bg-red-100 text-red-700',
            'normal' => 'bg-gray-100 text-gray-600',
            'low'    => 'bg-blue-100 text-blue-700',
            default  => 'bg-gray-100 text-gray-600',
        };
    }
}