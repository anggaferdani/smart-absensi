<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    protected $primaryKey = 'id';

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function absens() {
        return $this->hasMany(Absen::class);
    }

    public function izins() {
        return $this->hasMany(Izin::class);
    }

    public function lokasi() {
        return $this->belongsTo(Lokasi::class, 'lokasi_id');
    }

    public function unitKerja() {
        return $this->belongsTo(UnitKerja::class, 'unit_kerja_id');
    }
}
