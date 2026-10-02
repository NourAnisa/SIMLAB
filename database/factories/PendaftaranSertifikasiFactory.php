<?php

namespace Database\Factories;

use App\Models\PendaftaranSertifikasi;
use App\Models\User;
use App\Models\JadwalSertifikasi;
use Illuminate\Database\Eloquent\Factories\Factory;

class PendaftaranSertifikasiFactory extends Factory
{
    protected $model = PendaftaranSertifikasi::class;

    public function definition(): array
    {
        return [
            'user_id'        => User::factory(),
            'jadwal_id'      => JadwalSertifikasi::factory(),
            'dokumen_berkas' => 'berkas_fake.pdf',
            'status'         => 'menunggu',
        ];
    }
}
