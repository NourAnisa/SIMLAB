<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\SuratBebasLab;
use Illuminate\Http\Request;

class AdminSuratBebasLabController extends Controller
{
    public function index(Request $request)
    {
        $surats = SuratBebasLab::with('user')->latest()->paginate(15);
        return Inertia::render('Admin/SuratBebas/Index', [
            'surats' => $surats
        ]);
    }

    public function show(Request $request, SuratBebasLab $surat)
    {
        $surat->load('user');
        return Inertia::render('Admin/SuratBebas/Show', [
            'surat' => $surat
        ]);
    }

    public function verifikasi(Request $request, SuratBebasLab $surat)
    {
        $surat->update(['status' => 'diverifikasi']);
        return back()->with('success', 'Surat bebas lab berhasil diverifikasi.');
    }

    public function tandaiTanggungan(Request $request, SuratBebasLab $surat)
    {
        $surat->update([
            'status'  => 'masih_tanggungan',
            'catatan' => $request->catatan,
        ]);
        return back()->with('success', 'Permohonan ditandai masih memiliki tanggungan.');
    }
}
