<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role_id', 'status', 'photo',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function barangayAccount()
    {
        return $this->hasOne(BarangayAccount::class);
    }

    public function isAdmin(): bool
    {
        return in_array($this->role?->name, ['admin', 'superadmin']);
    }

    public function isBarangayUser(): bool
    {
        return $this->role?->name === 'barangay_user';
    }

    public function isViewer(): bool
    {
        return $this->role?->name === 'viewer';
    }

    public function isApproved(): bool
    {
        return $this->isAdmin() ||
               $this->barangayAccount?->approval_status === 'approved';
    }
}