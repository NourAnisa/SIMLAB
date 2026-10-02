<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\PendaftaranSertifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminSertifikasiController extends Controller
{
    public function index(Request $request)
    {
        $query = PendaftaranSertifikasi::with(['user', 'jadwal.skema'])->latest();
        if ($request->status) $query->where('status', $request->status);
        
        $pendaftarans = $query->paginate(15);
        
        $stats = [
            'total' => PendaftaranSertifikasi::count(),
            'menunggu' => PendaftaranSertifikasi::where('status', 'menunggu')->count(),
            'diverifikasi' => PendaftaranSertifikasi::where('status', 'diverifikasi')->count(),
            'lulus' => PendaftaranSertifikasi::where('status', 'lulus')->count(),
        ];
        return Inertia::render('Admin/Sertifikasi/Index', [
            'pendaftarans' => $pendaftarans,
            'stats' => $stats,
            'filters' => request()->only(['status'])
        ]);
    }

    public function show(Request $request, PendaftaranSertifikasi $pendaftaran)
    {
        $pendaftaran->load(['user', 'jadwal.skema']);
        return Inertia::render('Admin/Sertifikasi/Show', [
            'pendaftaran' => $pendaftaran
        ]);
    }

    public function verifikasi(Request $request, PendaftaranSertifikasi $pendaftaran)
    {
        $token = strtoupper(Str::random(6));
        $pendaftaran->update(['status' => 'diverifikasi', 'token_ujian' => $token]);
        
        return back()->with('success', "Diverifikasi. Token: {$token}");
    }

    public function hasilLulus(Request $request, PendaftaranSertifikasi $pendaftaran)
    {
        $pendaftaran->update(['status' => 'lulus']);
        
        return back()->with('success', 'Peserta dinyatakan lulus.');
    }

    public function hasilTidakLulus(Request $request, PendaftaranSertifikasi $pendaftaran)
    {
        $pendaftaran->update(['status' => 'tidak_lulus']);
        
        return back()->with('success', 'Peserta dinyatakan tidak lulus.');
    }

}
