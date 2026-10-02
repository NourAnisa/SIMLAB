<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkemaSertifikasi extends Model
{
    use HasFactory;
    protected $table = 'skema_sertifikasi';
    protected $fillable = ['nama_skema', 'deskripsi', 'logo'];

    public function jadwals()
    {
        return $this->hasMany(JadwalSertifikasi::class, 'skema_id');
    }
}
