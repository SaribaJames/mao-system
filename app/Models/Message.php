<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'mentioned_user_id',
        'message',
        'attachment_path',
        'attachment_name',
        'attachment_type',
        'attachment_size',
        'is_read',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function mentionedUser()
    {
        return $this->belongsTo(User::class, 'mentioned_user_id');
    }


        /**
     * Safely resolve the Cloudinary URL for this message's attachment.
     * Returns null instead of throwing if the asset can't be found or
     * Cloudinary can't be reached, so one missing attachment never
     * crashes the whole conversation view.
     */
    public function getAttachmentUrlAttribute(): ?string
    {
        if (!$this->attachment_path) {
            return null;
        }
        try {
            return \Illuminate\Support\Facades\Storage::disk('cloudinary')->url($this->attachment_path);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning(
                "Message #{$this->id}: could not resolve Cloudinary attachment URL for '{$this->attachment_path}': {$e->getMessage()}"
            );
            return null;
        }
    }
}