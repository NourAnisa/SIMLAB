<?php

namespace Database\Factories;

use App\Models\JadwalSertifikasi;
use Illuminate\Database\Eloquent\Factories\Factory;

class JadwalSertifikasiFactory extends Factory
{
    protected $model = JadwalSertifikasi::class;

    public function definition(): array
    {
        return [
            'skema_id'    => \App\Models\SkemaSertifikasi::factory(),
            'tanggal'     => now()->addDays(5)->format('Y-m-d'),
            'kuota'       => 20,
        ];
    }
}
