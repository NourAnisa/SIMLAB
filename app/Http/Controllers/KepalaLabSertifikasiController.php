<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\PendaftaranSertifikasi;
use Illuminate\Http\Request;

class KepalaLabSertifikasiController extends Controller
{
    public function index(Request $request)
    {
        $pendaftarans = PendaftaranSertifikasi::with(['user', 'jadwal.skema'])->where('status', 'diverifikasi')->paginate(15);
        return Inertia::render('KepalaLab/Sertifikasi/Index', [
            'pendaftarans' => $pendaftarans
        ]);
    }

    public function show(Request $request, PendaftaranSertifikasi $pendaftaran)
    {
        $pendaftaran->load(['user', 'jadwal.skema']);
        return Inertia::render('KepalaLab/Sertifikasi/Show', [
            'pendaftaran' => $pendaftaran
        ]);
    }

    public function nilai(Request $request, PendaftaranSertifikasi $pendaftaran)
    {
        $request->validate([
            'status'          => 'required|in:lulus,tidak_lulus',
            'nilai'           => 'required|integer|min:0|max:100',
            'hasil_validasi'  => 'nullable|string|max:1000',
        ]);
        $pendaftaran->update([
            'status'         => $request->status,
            'nilai'          => $request->nilai,
            'hasil_validasi' => $request->hasil_validasi,
        ]);
        
        return redirect()->route('kepala_lab.sertifikasi.index')->with('success', 'Penilaian berhasil disimpan.');
    }
}
