<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramAchievement extends Model
{
    protected $fillable = [
        'program_id',
        'photo_path',
        'caption',
        'posted_by',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function postedBy()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }
}