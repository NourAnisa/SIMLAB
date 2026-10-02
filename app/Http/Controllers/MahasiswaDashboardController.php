<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\ProfileValidationService;
use App\Models\IzinPenelitian;
use App\Models\LogAktivitas;
use App\Models\PeminjamanLab;
use App\Models\PendaftaranSertifikasi;
use App\Models\SuratBebasLab;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class MahasiswaDashboardController extends Controller
{
    public function __construct(
        private ProfileValidationService $validationService
    ) {}

    public function index(\Illuminate\Http\Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $data = [
            'pendingSertifikasi' => PendaftaranSertifikasi::where('user_id', $user->id)->where('status', 'menunggu')->count(),
            'pendingIzin'        => IzinPenelitian::where('user_id', $user->id)->where('status', 'menunggu')->count(),
            'pendingPeminjaman'  => PeminjamanLab::where('user_id', $user->id)->where('status', 'menunggu')->count(),
            'pendingSurat'       => SuratBebasLab::where('user_id', $user->id)->where('status', 'menunggu')->count(),
            'logAktivitas'       => LogAktivitas::where('user_id', $user->id)->latest()->take(5)->get(),
            'profileCheck'       => $this->validationService->canAccessFeatures($user),
        ];

        return Inertia::render('Mahasiswa/Dashboard', $data);
    }
}
