<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\PeminjamanLab;

class KepalaLabPeminjamanLabController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $peminjamans = PeminjamanLab::with('user')->where('status', 'diverifikasi')->latest()->paginate(15);
        return Inertia::render('KepalaLab/PeminjamanLab/Index', [
            'peminjamans' => $peminjamans
        ]);
    }

    public function show(\Illuminate\Http\Request $request, PeminjamanLab $peminjaman)
    {
        $peminjaman->load('user');
        return Inertia::render('KepalaLab/PeminjamanLab/Show', [
            'peminjaman' => $peminjaman
        ]);
    }

    public function setujui(\Illuminate\Http\Request $request, PeminjamanLab $peminjaman)
    {
        if ($peminjaman->status !== 'diverifikasi') {
            return back()->with('error', 'Peminjaman tidak dalam status diverifikasi.');
        }

        $peminjaman->update(['status' => 'disetujui']);

        return back()->with('success', 'Peminjaman disetujui.');
    }

    public function tolak(\Illuminate\Http\Request $request, PeminjamanLab $peminjaman)
    {
        $peminjaman->update(['status' => 'ditolak']);

        return back()->with('success', 'Peminjaman ditolak.');
    }
}
