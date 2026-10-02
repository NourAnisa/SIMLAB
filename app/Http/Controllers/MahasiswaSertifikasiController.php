<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\JadwalSertifikasi;
use App\Models\PendaftaranSertifikasi;
use Illuminate\Http\Request;

class MahasiswaSertifikasiController extends Controller
{
    public function index(Request $request)
    {
        // Removed `where('tanggal', '>=', today())` temporarily so existing dummy data appears
        $jadwals = JadwalSertifikasi::with('skema')->paginate(10);
        $jadwals->getCollection()->transform(function ($jadwal) {
            $jadwal->sisa_kuota = $jadwal->sisa_kuota;
            $jadwal->sudah_daftar = $jadwal->pendaftarans()->where('user_id', auth()->id())->exists();
            return $jadwal;
        });

        return Inertia::render('Mahasiswa/Sertifikasi/Index', [
            'jadwals' => $jadwals
        ]);
    }

    public function daftarForm(Request $request, JadwalSertifikasi $jadwal)
    {
        $jadwal->load('skema');
        return Inertia::render('Mahasiswa/Sertifikasi/Daftar', [
            'jadwal' => $jadwal
        ]);
    }

    public function daftarStore(Request $request, JadwalSertifikasi $jadwal)
    {
        $request->validate(['dokumen_berkas' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048']);
        $path = null;
        if ($request->hasFile('dokumen_berkas')) {
            $path = $request->file('dokumen_berkas')->store('sertifikasi', 'public');
        }
        $pendaftaran = PendaftaranSertifikasi::create([
            'user_id'        => auth()->id(),
            'jadwal_id'      => $jadwal->id,
            'dokumen_berkas' => $path,
            'status'         => 'menunggu',
        ]);
        

        return redirect()->route('mahasiswa.sertifikasi.riwayat')->with('success', 'Pendaftaran berhasil dikirim.');
    }

    public function riwayat(Request $request)
    {
        $pendaftarans = PendaftaranSertifikasi::with('jadwal.skema')->where('user_id', auth()->id())->latest()->paginate(10);
        return Inertia::render('Mahasiswa/Sertifikasi/Riwayat', [
            'pendaftarans' => $pendaftarans
        ]);
    }

    public function cetak(PendaftaranSertifikasi $pendaftaran)
    {
        abort_if($pendaftaran->user_id !== auth()->id(), 403);
        abort_if(!in_array($pendaftaran->status, ['lulus', 'tidak_lulus']), 403);
        $pendaftaran->load(['user', 'jadwal.skema']);
        $kepalaLab = \App\Models\User::where('role', 'kepala_lab')->first();
        return view('mahasiswa.sertifikasi.cetak', compact('pendaftaran', 'kepalaLab'));
    }

}

