<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratBebasLab extends Model
{
    use HasFactory;
    protected $table = 'surat_bebas_lab';
    protected $fillable = ['user_id', 'tanggal_pengajuan', 'status', 'catatan', 'tanggal_disetujui'];
    protected $casts = ['tanggal_pengajuan' => 'date', 'tanggal_disetujui' => 'date'];

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
