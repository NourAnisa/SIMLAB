<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\IzinPenelitian;
use Illuminate\Http\Request;

class KepalaLabIzinPenelitianController extends Controller
{
    public function index(Request $request)
    {
        $query = IzinPenelitian::with('user')->whereIn('status', ['diverifikasi', 'disetujui', 'ditolak'])->latest();
        if ($request->status) {
            $query->where('status', $request->status);
        }
        $izins = $query->paginate(15);
        $counts = [
            'all'          => IzinPenelitian::whereIn('status', ['diverifikasi', 'disetujui', 'ditolak'])->count(),
            'diverifikasi' => IzinPenelitian::where('status', 'diverifikasi')->count(),
            'disetujui'    => IzinPenelitian::where('status', 'disetujui')->count(),
            'ditolak'      => IzinPenelitian::where('status', 'ditolak')->count(),
        ];

        return Inertia::render('KepalaLab/IzinPenelitian/Index', [
            'izins' => $izins,
            'counts' => $counts,
            'filters' => $request->only('status')
        ]);
    }

    public function show(Request $request, IzinPenelitian $izin)
    {
        $izin->load('user');
        return Inertia::render('KepalaLab/IzinPenelitian/Show', [
            'izin' => $izin
        ]);
    }

    public function setujui(Request $request, IzinPenelitian $izin)
    {
        if ($izin->status !== 'diverifikasi') {
            return back()->with('error', 'Izin penelitian harus diverifikasi admin terlebih dahulu.');
        }

        $izin->update(['status' => 'disetujui', 'tanggal_disetujui' => today()]);

        return back()->with('success', 'Izin penelitian disetujui.');
    }

    public function tolak(Request $request, IzinPenelitian $izin)
    {
        $request->validate(['catatan_verifikasi' => 'required']);
        $izin->update(['status' => 'ditolak', 'catatan_verifikasi' => $request->catatan_verifikasi]);

        return back()->with('success', 'Izin penelitian ditolak.');
    }
}
