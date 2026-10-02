<?php

namespace Database\Factories;

use App\Models\SkemaSertifikasi;
use Illuminate\Database\Eloquent\Factories\Factory;

class SkemaSertifikasiFactory extends Factory
{
    protected $model = SkemaSertifikasi::class;

    public function definition(): array
    {
        return [
            'nama_skema' => 'Skema Dummy',
            'deskripsi'  => 'Deskripsi dummy',
        ];
    }
}
