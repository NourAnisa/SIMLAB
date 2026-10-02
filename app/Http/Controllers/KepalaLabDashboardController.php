<?php
namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\IzinPenelitian;
use App\Models\PeminjamanLab;
use App\Models\PendaftaranSertifikasi;
use App\Models\SuratBebasLab;
use Illuminate\Http\Request;

class KepalaLabDashboardController extends Controller
{
    public function index(Request $request)
    {
        $data = [
            'totalSertifikasi' => PendaftaranSertifikasi::where('status', 'diverifikasi')->count(),
            'totalIzin'        => IzinPenelitian::where('status', 'diverifikasi')->count(),
            'totalPeminjaman'  => PeminjamanLab::where('status', 'diverifikasi')->count(),
            'totalSurat'       => SuratBebasLab::where('status', 'diverifikasi')->count(),
            'pendingSertifikasi' => PendaftaranSertifikasi::with(['user','jadwal.skema'])->where('status','diverifikasi')->latest()->get(),
            'pendingIzin'       => IzinPenelitian::with('user')->where('status','diverifikasi')->latest()->get(),
            'pendingPeminjaman' => PeminjamanLab::with('user')->where('status','diverifikasi')->latest()->get(),
            'pendingSurat'      => SuratBebasLab::with('user')->where('status','diverifikasi')->latest()->get(),
        ];

        return Inertia::render('KepalaLab/Dashboard', $data);
    }
}
