<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransaction extends Model
{
    protected $fillable = [
        'stock_id', 'type', 'quantity',
        'recipient', 'farmer_id', 'notes', 'processed_by',
        'partner_name', 'reference_number', 'received_date', 'attachment_path',
    ];

    protected $casts = [
        'received_date' => 'date',
    ];

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    public function farmer()
    {
        return $this->belongsTo(Farmer::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Safely resolve the Cloudinary URL for this transaction's proof-of-delivery
     * attachment. Returns null instead of throwing if it's missing or Cloudinary
     * can't be reached, so a broken attachment never breaks the receipt report.
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
                "StockTransaction #{$this->id}: could not resolve Cloudinary attachment URL for '{$this->attachment_path}': {$e->getMessage()}"
            );
            return null;
        }
    }
}
