<?php

namespace Database\Factories;

use App\Models\IzinPenelitian;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class IzinPenelitianFactory extends Factory
{
    protected $model = IzinPenelitian::class;

    public function definition(): array
    {
        return [
            'user_id'          => User::factory(),
            'judul_penelitian' => $this->faker->sentence(),
            'tujuan'           => $this->faker->paragraph(),
            'dokumen_berkas'   => null,
            'data_dibutuhkan'  => ['Data A', 'Data B'],
            'status'           => 'menunggu',
        ];
    }
}
