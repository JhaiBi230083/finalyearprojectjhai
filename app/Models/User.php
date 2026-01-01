<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // Keep this for backward compatibility
        'phone_number',
        'faculty',
        'matric_number',
        'canteen_id', // Add this
        'is_active', // Add this if missing
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean', // Add this
    ];

    // Relationships
    public function canteens()
    {
        return $this->hasMany(Canteen::class, 'vendor_id');
    }

    // Add this one-to-one relationship
    public function canteen()
    {
        return $this->belongsTo(Canteen::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class);
    }

    // Scopes
    public function scopeStudents($query)
    {
        return $query->where('role', 'student');
    }

    public function scopeVendors($query)
    {
        return $query->where('role', 'vendor');
    }

    public function scopeStaff($query)
    {
        return $query->where('role', 'staff');
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    // Helper methods for role checking
    public function isAdmin(): bool
    {
        return $this->hasRole('admin') || $this->role === 'admin';
    }

    public function isVendor(): bool
    {
        return $this->hasRole('vendor') || $this->role === 'vendor';
    }

    public function isStudent(): bool
    {
        return $this->hasRole('student') || $this->role === 'student';
    }

    public function isStaff(): bool
    {
        return $this->hasRole('staff') || $this->role === 'staff';
    }
}
