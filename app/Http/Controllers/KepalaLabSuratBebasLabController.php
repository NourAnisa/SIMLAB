<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\SuratBebasLab;
use App\Models\User;

class KepalaLabSuratBebasLabController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $surats = SuratBebasLab::with('user')->where('status', 'diverifikasi')->latest()->paginate(15);
        return Inertia::render('KepalaLab/SuratBebasLab/Index', [
            'surats' => $surats
        ]);
    }

    public function show(\Illuminate\Http\Request $request, SuratBebasLab $surat)
    {
        $surat->load('user');
        return Inertia::render('KepalaLab/SuratBebasLab/Show', [
            'surat' => $surat
        ]);
    }

    public function setujui(\Illuminate\Http\Request $request, SuratBebasLab $surat)
    {
        if ($surat->status !== 'diverifikasi') {
            return back()->with('error', 'Surat tidak dalam status diverifikasi.');
        }

        $surat->update(['status' => 'disetujui', 'tanggal_disetujui' => today()]);

        return back()->with('success', 'Surat bebas lab disetujui.');
    }

    public function cetak(SuratBebasLab $surat)
    {
        abort_if($surat->status !== 'disetujui', 403);
        $surat->load('user');
        $kepalaLab = User::where('role', 'kepala_lab')->first();
        return view('kepala_lab.surat_bebas.cetak', compact('surat', 'kepalaLab'));
    }
}
