<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalSertifikasi extends Model
{
    use HasFactory;
    protected $table = 'jadwal_sertifikasi';
    protected $fillable = ['skema_id', 'tanggal', 'kuota'];
    protected $casts = ['tanggal' => 'date'];

    public function skema() { return $this->belongsTo(SkemaSertifikasi::class, 'skema_id'); }
    public function pendaftarans() { return $this->hasMany(PendaftaranSertifikasi::class, 'jadwal_id'); }

    public function getSisaKuotaAttribute(): int
    {
        return $this->kuota - $this->pendaftarans()->whereNotIn('status', ['tidak_lulus'])->count();
    }

    protected static function booted()
    {
        static::created(function ($jadwal) {
            $jadwal->load('skema');
            $mahasiswas = \App\Models\User::where('role', 'mahasiswa')->get();
            \Illuminate\Support\Facades\Notification::send($mahasiswas, new \App\Notifications\JadwalBaruNotification($jadwal));
        });
    }
}
