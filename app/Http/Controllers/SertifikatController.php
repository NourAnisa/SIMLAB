<?php

namespace App\Http\Controllers;

use App\Models\PendaftaranSertifikasi;
use Illuminate\Http\Request;

class SertifikatController extends Controller
{
    public function verifikasi($id)
    {
        $pendaftaran = PendaftaranSertifikasi::with(['user', 'jadwal.skema'])->findOrFail($id);

        return response()->json([
            'valid' => $pendaftaran->status === 'lulus',
            'peserta' => $pendaftaran->user->name,
            'skema' => $pendaftaran->jadwal->skema->nama_skema,
            'tanggal' => $pendaftaran->jadwal->tanggal,
            'status' => $pendaftaran->status,
        ]);
    }
}
