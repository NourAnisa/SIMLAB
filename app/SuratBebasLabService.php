<?php

namespace App;

use App\Models\PeminjamanLab;
use App\Models\SuratBebasLab;
use App\Models\User;

class SuratBebasLabService
{
    public function submit(User $user): SuratBebasLab
    {
        // Tanggungan aktif = peminjaman yang disetujui dan belum dikembalikan
        $hasTanggungan = PeminjamanLab::where('user_id', $user->id)
            ->whereIn('status', ['disetujui', 'menunggu_kembali'])
            ->exists();

        if ($hasTanggungan) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'error' => 'Anda masih memiliki tanggungan peminjaman lab aktif. Harap kembalikan barang/ruangan sebelum mengajukan surat bebas lab.',
            ]);
        }

        return SuratBebasLab::create([
            'user_id'           => $user->id,
            'tanggal_pengajuan' => today(),
            'status'            => 'menunggu',
        ]);
    }
}
