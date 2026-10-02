<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IzinPenelitian extends Model
{
    use HasFactory;
    protected $table = 'izin_penelitian';
    protected $fillable = ['user_id', 'judul_penelitian', 'tujuan', 'dokumen_berkas', 'data_dibutuhkan', 'status', 'catatan_verifikasi', 'tanggal_disetujui'];
    protected $casts = ['tanggal_disetujui' => 'date', 'data_dibutuhkan' => 'array'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function scopePending($query)
    {
        return $query->where('status', 'menunggu');
    }
    public function scopeApproved($query)
    {
        return $query->where('status', 'disetujui');
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
