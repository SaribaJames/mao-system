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

    /**
     * Safely resolve the Cloudinary URL for this achievement's photo.
     * Returns null (instead of throwing) if the asset can't be found or
     * Cloudinary can't be reached, so a single missing/broken photo never
     * takes down a whole page of achievements.
     */
    public function getPhotoUrlAttribute(): ?string
    {
        try {
            return \Illuminate\Support\Facades\Storage::disk('cloudinary')->url($this->photo_path);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning(
                "ProgramAchievement #{$this->id}: could not resolve Cloudinary photo URL for '{$this->photo_path}': {$e->getMessage()}"
            );
            return null;
        }
    }
}