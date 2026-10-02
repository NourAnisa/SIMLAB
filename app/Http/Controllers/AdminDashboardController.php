<?php
namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\IzinPenelitian;
use App\Models\PeminjamanLab;
use App\Models\PendaftaranSertifikasi;
use App\Models\SuratBebasLab;

class AdminDashboardController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $data = [
            'totalSertifikasi' => PendaftaranSertifikasi::where('status', 'menunggu')->count(),
            'totalIzin'        => IzinPenelitian::where('status', 'menunggu')->count(),
            'totalPeminjaman'  => PeminjamanLab::where('status', 'menunggu')->count(),
            'totalSurat'       => SuratBebasLab::where('status', 'menunggu')->count(),
            'jadwalHariIni'    => PeminjamanLab::with('user')->where('tanggal_pinjam', today())->where('status', 'disetujui')->get(),
        ];

        return Inertia::render('Admin/Dashboard', $data);
    }
}
