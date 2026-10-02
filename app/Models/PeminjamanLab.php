<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeminjamanLab extends Model
{
    use HasFactory;
    protected $table = 'peminjaman_lab';
    protected $fillable = ['user_id', 'ruangan', 'tanggal_pinjam', 'jam_mulai', 'jam_selesai', 'keperluan', 'status', 'surat_keterangan', 'waktu_dikembalikan', 'waktu_diterima_admin'];
    protected $casts = [
        'tanggal_pinjam' => 'date',
        'waktu_dikembalikan' => 'datetime',
        'waktu_diterima_admin' => 'datetime'
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function scopePending($query) { return $query->where('status', 'menunggu'); }
    public function scopeApproved($query) { return $query->where('status', 'disetujui'); }

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
