<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barangay extends Model
{
    protected $fillable = ['name', 'contact_number'];

    public function accounts()
    {
        return $this->hasMany(BarangayAccount::class);
    }
}