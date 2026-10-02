<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\IzinPenelitian;
use Illuminate\Http\Request;

class AdminIzinPenelitianController extends Controller
{
    public function index(Request $request)
    {
        $query = IzinPenelitian::with('user')->latest();
        if ($request->status) {
            $query->where('status', $request->status);
        }
        $izins = $query->paginate(15);
        $counts = [
            'all'          => IzinPenelitian::count(),
            'menunggu'     => IzinPenelitian::where('status', 'menunggu')->count(),
            'diverifikasi' => IzinPenelitian::where('status', 'diverifikasi')->count(),
            'disetujui'    => IzinPenelitian::where('status', 'disetujui')->count(),
            'ditolak'      => IzinPenelitian::where('status', 'ditolak')->count(),
        ];
        
        return Inertia::render('Admin/IzinPenelitian/Index', [
            'izins' => $izins,
            'counts' => $counts,
            'filters' => request()->only(['status'])
        ]);
    }

    public function show(Request $request, IzinPenelitian $izinPenelitian)
    {
        $izinPenelitian->load('user');
        
        return Inertia::render('Admin/IzinPenelitian/Show', [
            'izin' => $izinPenelitian
        ]);
    }

    public function verifikasi(Request $request, IzinPenelitian $izinPenelitian)
    {
        $izinPenelitian->update(['status' => 'diverifikasi']);
        return back()->with('success', 'Izin penelitian berhasil diverifikasi. Menunggu persetujuan Kepala Lab.');
    }

    public function tolak(Request $request, IzinPenelitian $izinPenelitian)
    {
        $request->validate(['catatan_verifikasi' => 'required|string|max:500']);
        $izinPenelitian->update([
            'status'              => 'ditolak',
            'catatan_verifikasi'  => $request->catatan_verifikasi,
        ]);
        return back()->with('success', 'Izin penelitian telah ditolak.');
    }
}
