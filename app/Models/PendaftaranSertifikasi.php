<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendaftaranSertifikasi extends Model
{
    use HasFactory;
    protected $table = 'pendaftaran_sertifikasi';
    protected $fillable = ['user_id', 'jadwal_id', 'dokumen_berkas', 'status', 'token_ujian', 'jawaban_ujian', 'hasil_validasi', 'nilai'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function jadwal()
    {
        return $this->belongsTo(JadwalSertifikasi::class, 'jadwal_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'menunggu');
    }
    public function scopeApproved($query)
    {
        return $query->where('status', 'lulus');
    }

    protected static function booted()
    {
        static::updated(function ($model) {
            if ($model->isDirty('status') && $model->user) {
                $model->user->notify(new \App\Notifications\StatusUpdatedNotification(
                    class_basename($model), 
                    $model->status, 
                    $model->id
                ));
            }
        });
    }
}
