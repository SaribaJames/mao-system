<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'status',
        'photo',
        'pin',
    ];

    protected $hidden = [
        'password',
        'pin',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'pin' => 'hashed',
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
        return $this->role?->name === 'admin';
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

    public function sentMessages()
    {
        return $this->hasMany(\App\Models\Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(\App\Models\Message::class, 'receiver_id');
    }

    public function assignedPrograms()
    {
        return $this->hasMany(Program::class, 'assigned_user_id');
    }

    public function hasAssignedProgram(): bool
    {
        return $this->assignedPrograms()->exists();
    }

    public function hasPin(): bool
    {
        return ! empty($this->pin);
    }

    public function verifyPin(?string $pin): bool
    {
        return $this->hasPin() && $pin && \Illuminate\Support\Facades\Hash::check($pin, $this->pin);
    }
}