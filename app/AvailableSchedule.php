<?php

namespace App;

use App\Models\PeminjamanLab;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AvailableSchedule implements ValidationRule
{
    public function __construct(
        private string $ruangan,
        private string $tanggal,
        private string $jamMulai,
        private string $jamSelesai,
        private ?int $excludeId = null
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $conflict = PeminjamanLab::where('ruangan', $this->ruangan)
            ->where('tanggal_pinjam', $this->tanggal)
            ->whereIn('status', ['menunggu', 'disetujui', 'menunggu_kembali', 'diverifikasi'])
            ->where(function ($q) {
                $q->where(function ($q2) {
                    $q2->where('jam_mulai', '<', $this->jamSelesai)
                        ->where('jam_selesai', '>', $this->jamMulai);
                });
            })
            ->when($this->excludeId, fn($q) => $q->where('id', '!=', $this->excludeId))
            ->exists();

        if ($conflict) {
            $fail('Ruangan sudah dipesan pada waktu tersebut.');
        }
    }
}
