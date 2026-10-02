<?php
namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\PeminjamanLab;
use Illuminate\Http\Request;

class DosenDashboardController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        $data = [
            'totalPeminjaman'  => PeminjamanLab::where('user_id', $user->id)->count(),
            'totalDisetujui'   => PeminjamanLab::where('user_id', $user->id)->where('status','disetujui')->count(),
            'totalDitolak'     => PeminjamanLab::where('user_id', $user->id)->where('status','ditolak')->count(),
            'totalMenunggu'    => PeminjamanLab::where('user_id', $user->id)->where('status','menunggu')->count(),
            'riwayatPeminjaman'=> PeminjamanLab::where('user_id', $user->id)->latest()->take(5)->get(),
        ];

        return Inertia::render('Dosen/Dashboard', $data);
    }
}
