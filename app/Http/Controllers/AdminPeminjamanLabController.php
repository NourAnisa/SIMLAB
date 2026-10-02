<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\PeminjamanLab;
use Illuminate\Http\Request;

class AdminPeminjamanLabController extends Controller
{
    public function index(Request $request)
    {
        $query = PeminjamanLab::with('user')->latest();
        if ($request->tanggal) $query->where('tanggal_pinjam', $request->tanggal);
        if ($request->ruangan) $query->where('ruangan', 'like', "%{$request->ruangan}%");
        if ($request->status) $query->where('status', $request->status);
        
        $peminjamans = $query->paginate(15);
        
        return Inertia::render('Admin/PeminjamanLab/Index', [
            'peminjamans' => $peminjamans,
            'filters' => request()->only(['tanggal', 'ruangan', 'status'])
        ]);
    }

    public function show(Request $request, PeminjamanLab $peminjaman)
    {
        $peminjaman->load('user');
        
        return Inertia::render('Admin/PeminjamanLab/Show', [
            'peminjaman' => $peminjaman
        ]);
    }

    public function verifikasi(Request $request, PeminjamanLab $peminjaman)
    {
        $peminjaman->update(['status' => 'diverifikasi']);
        return back()->with('success', 'Peminjaman berhasil diverifikasi.');
    }

    public function tolak(Request $request, PeminjamanLab $peminjaman)
    {
        $peminjaman->update(['status' => 'ditolak']);
        return back()->with('success', 'Peminjaman ditolak.');
    }

    public function verifikasiKembali(Request $request, PeminjamanLab $peminjaman)
    {
        $peminjaman->update([
            'status' => 'dikembalikan',
            'waktu_diterima_admin' => now()
        ]);
        return back()->with('success', 'Pengembalian ruangan berhasil diverifikasi.');
    }
}
