<?php

namespace Database\Factories;

use App\Models\PeminjamanLab;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PeminjamanLabFactory extends Factory
{
    protected $model = PeminjamanLab::class;

    public function definition(): array
    {
        return [
            'user_id'          => User::factory(),
            'ruangan'          => 'Lab Komputer A',
            'tanggal_pinjam'   => now()->addDays(2)->format('Y-m-d'),
            'jam_mulai'        => '08:00:00',
            'jam_selesai'      => '10:00:00',
            'keperluan'        => 'Praktikum',
            'surat_keterangan' => null,
            'status'           => 'menunggu',
        ];
    }
}
