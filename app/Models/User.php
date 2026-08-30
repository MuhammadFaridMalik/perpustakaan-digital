<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function siswaProfile()
    {
        return $this->hasOne(SiswaProfile::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'siswa_id');
    }

    public function borrowingsAsSiswa()
    {
        return $this->hasMany(Borrowing::class, 'siswa_id');
    }

    public function borrowingsAsAdmin()
    {
        return $this->hasMany(Borrowing::class, 'admin_id');
    }

    public function libraryNotifications()
    {
        return $this->hasMany(Notification::class);
    }
}
