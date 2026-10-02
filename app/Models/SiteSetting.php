<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $table = 'site_settings';
    protected $fillable = [
        'nama_website', 'nama_kampus', 'logo',
        'deskripsi', 'alamat', 'telepon', 'email_kontak',
        'jam_operasional', 'facebook', 'instagram', 'twitter', 'youtube',
        'gallery_images', 'daftar_ruangan', 'tema_warna',
        'domain_email_mahasiswa', 'domain_email_pegawai',
    ];

    protected $casts = [
        'gallery_images' => 'array',
    ];

    public static function getSetting(): self
    {
        return self::firstOrNew(['id' => 1], [
            'nama_website'    => 'SIMLAB',
            'nama_kampus'     => 'STMIK Samarinda',
            'logo'            => null,
            'deskripsi'       => null,
            'alamat'          => null,
            'telepon'         => null,
            'email_kontak'    => null,
            'jam_operasional' => null,
            'facebook'        => null,
            'instagram'       => null,
            'twitter'         => null,
            'youtube'         => null,
            'daftar_ruangan'  => 'Lab Komputer 1, Lab Komputer 2, Lab Jaringan, Lab Multimedia, Ruang Rapat Lab',
            'tema_warna'      => 'ungu',
            'domain_email_mahasiswa' => null,
            'domain_email_pegawai' => null,
        ]);
    }
}
