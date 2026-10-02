<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['name', 'nim_nip', 'jurusan', 'email', 'role', 'password', 'foto_profil', 'tanda_tangan', 'email_verified_at', 'profile_completed'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // --- Role Helper Methods ---

    public function isMahasiswa(): bool
    {
        return $this->role === 'mahasiswa';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isKepalaLab(): bool
    {
        return $this->role === 'kepala_lab';
    }

    public function isDosen(): bool
    {
        return $this->role === 'dosen';
    }

    public function hasRole(string|array $roles): bool
    {
        if (is_array($roles)) {
            return in_array($this->role, $roles);
        }

        return $this->role === $roles;
    }

    public function getDashboardRoute(): string
    {
        return match ($this->role) {
            'admin'      => route('admin.dashboard'),
            'kepala_lab' => route('kepala_lab.dashboard'),
            'dosen'      => route('dosen.dashboard'),
            default      => route('mahasiswa.dashboard'),
        };
    }

    public function pendaftaranSertifikasi()
    {
        return $this->hasMany(PendaftaranSertifikasi::class);
    }
    public function izinPenelitian()
    {
        return $this->hasMany(IzinPenelitian::class);
    }
    public function peminjamanLab()
    {
        return $this->hasMany(PeminjamanLab::class);
    }
    public function suratBebasLab()
    {
        return $this->hasMany(SuratBebasLab::class);
    }
    public function logAktivitas()
    {
        return $this->hasMany(LogAktivitas::class);
    }
    public function beritas()
    {
        return $this->hasMany(Berita::class);
    }
}
