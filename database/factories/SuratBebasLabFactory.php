<?php

namespace Database\Factories;

use App\Models\SuratBebasLab;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SuratBebasLabFactory extends Factory
{
    protected $model = SuratBebasLab::class;

    public function definition(): array
    {
        return [
            'user_id'           => User::factory(),
            'tanggal_pengajuan' => now()->format('Y-m-d'),
            'status'            => 'menunggu',
            'catatan'           => null,
        ];
    }
}
