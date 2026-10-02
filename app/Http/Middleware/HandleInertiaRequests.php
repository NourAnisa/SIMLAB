<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     * Menggunakan blade khusus yang sudah kita buat tadi, jadi
     * tidak menimpa welcome.blade.php milik SPA.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'inertia-app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request): array
    {
        $siteSetting = \Illuminate\Support\Facades\Cache::rememberForever('site_setting', function () {
            return \App\Models\SiteSetting::first();
        });

        $pendingCounts = [];
        if ($request->user()) {
            if ($request->user()->role === 'admin') {
                $pendingCounts = [
                    'sertifikasi' => \App\Models\PendaftaranSertifikasi::where('status', 'menunggu')->count(),
                    'peminjaman_lab' => \App\Models\PeminjamanLab::where('status', 'menunggu')->count(),
                    'izin_penelitian' => \App\Models\IzinPenelitian::where('status', 'menunggu')->count(),
                    'surat_bebas_lab' => \App\Models\SuratBebasLab::where('status', 'menunggu')->count(),
                ];
            } elseif ($request->user()->role === 'kepala_lab') {
                $pendingCounts = [
                    'sertifikasi' => \App\Models\PendaftaranSertifikasi::where('status', 'diverifikasi')->count(),
                    'peminjaman_lab' => \App\Models\PeminjamanLab::where('status', 'diverifikasi')->count(),
                    'izin_penelitian' => \App\Models\IzinPenelitian::where('status', 'diverifikasi')->count(),
                    'surat_bebas_lab' => \App\Models\SuratBebasLab::where('status', 'diverifikasi')->count(),
                ];
            } elseif (in_array($request->user()->role, ['mahasiswa', 'dosen'])) {
                $userId = $request->user()->id;
                $sevenDaysAgo = now()->subDays(7);
                $pendingCounts = [
                    'sertifikasi' => \App\Models\PendaftaranSertifikasi::where('user_id', $userId)->where('updated_at', '>=', $sevenDaysAgo)->where('status', '!=', 'menunggu')->count(),
                    'peminjaman_lab' => \App\Models\PeminjamanLab::where('user_id', $userId)->where('updated_at', '>=', $sevenDaysAgo)->where('status', '!=', 'menunggu')->count(),
                    'izin_penelitian' => \App\Models\IzinPenelitian::where('user_id', $userId)->where('updated_at', '>=', $sevenDaysAgo)->where('status', '!=', 'menunggu')->count(),
                    'surat_bebas_lab' => \App\Models\SuratBebasLab::where('user_id', $userId)->where('updated_at', '>=', $sevenDaysAgo)->where('status', '!=', 'menunggu')->count(),
                ];
            }
        }

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'role' => $request->user()->role,
                    'foto_profil' => $request->user()->foto_profil,
                ] : null,
            ],
            'siteSetting' => $siteSetting ?? (object)[],
            'pendingCounts' => $pendingCounts,
            'flash' => [
                'success' => $request->session()->get('success'),
                'error'   => $request->session()->get('error'),
                'status'  => $request->session()->get('status'),
            ],
        ]);
    }
}
