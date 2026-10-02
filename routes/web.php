<?php

use App\Http\Controllers\AdminBeritaController;
use App\Http\Controllers\AdminSiteSettingController;
use App\Http\Controllers\AdminIzinPenelitianController;
use App\Http\Controllers\AdminJadwalController;
use App\Http\Controllers\AdminPeminjamanLabController;
use App\Http\Controllers\AdminSertifikasiController;
use App\Http\Controllers\AdminSkemaController;
use App\Http\Controllers\AdminSuratBebasLabController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminRuanganController;
use App\Http\Controllers\AdminProgramStudiController;
use App\Http\Controllers\KepalaLabIzinPenelitianController;
use App\Http\Controllers\KepalaLabPeminjamanLabController;
use App\Http\Controllers\KepalaLabSertifikasiController;
use App\Http\Controllers\KepalaLabStrukturController;
use App\Http\Controllers\KepalaLabSuratBebasLabController;
use App\Http\Controllers\MahasiswaIzinPenelitianController;
use App\Http\Controllers\MahasiswaSertifikasiController;
use App\Http\Controllers\MahasiswaSuratBebasLabController;
use App\Http\Controllers\DosenDashboardController;
use App\Http\Controllers\DosenModulController;
use App\Http\Controllers\DosenPeminjamanLabController;
use App\Http\Controllers\PeminjamanLabController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SertifikatController;
use App\Http\Controllers\StoredFileController;
use App\Http\Controllers\InertiaDemoController;
use Illuminate\Support\Facades\Route;

use Inertia\Inertia;
// ─── PUBLIC SERTIFIKAT VERIFIKASI ────────────────────────────
Route::get('/verifikasi-sertifikat/{id}', [SertifikatController::class, 'verifikasi'])
    ->name('sertifikat.verifikasi');

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'skemas' => \App\Models\SkemaSertifikasi::orderBy('nama_skema')->get(),
        'jadwalSertifikasi' => \App\Models\JadwalSertifikasi::with('skema')
            ->withCount(['pendaftarans' => fn($q) => $q->whereNotIn('status', ['tidak_lulus'])])
            ->where('tanggal', '>=', today())
            ->orderBy('tanggal')
            ->take(6)->get(),
        'jadwalPeminjaman' => \App\Models\PeminjamanLab::whereIn('status', ['disetujui', 'diverifikasi'])
            ->where('tanggal_pinjam', '>=', today()->subDays(7))
            ->orderBy('tanggal_pinjam')
            ->take(9)->get(),
        'beritas' => \App\Models\Berita::with('user')->latest()->take(6)->get(),
        'moduls' => \App\Models\Modul::with('user')->latest()->take(8)->get(),
        'strukturs' => \App\Models\StrukturOrganisasi::orderBy('urutan')->orderBy('id')->get()
    ]);
})->name('home');

Route::get('/dashboard', function () {
    return redirect()->to(request()->user()->getDashboardRoute());
})->middleware('auth')->name('dashboard');

// ─── FILE SERVE (no symlink needed) ──────────────────────────
Route::get('/files/{path}', [StoredFileController::class, 'servePrivate'])
    ->where('path', '.*')
    ->middleware('auth')
    ->name('files.serve');

// ─── PUBLIC FILE SERVE (gambar publik: skema, berita, struktur, dll) ──────────────────────────
Route::get('/storage-public/{path}', [StoredFileController::class, 'servePublic'])
    ->where('path', '.*')
    ->name('storage.public');

// ─── MAHASISWA ───────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'role:mahasiswa'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\MahasiswaDashboardController::class, 'index'])
        ->name('dashboard');

    // Routes yang memerlukan profil lengkap (dengan middleware ProfileCompletionMiddleware)
    Route::middleware(['profile-completion', \App\Http\Middleware\HandleInertiaRequests::class])->group(function () {
        Route::get('/sertifikasi', [MahasiswaSertifikasiController::class, 'index'])->name('sertifikasi.index');
        Route::get('/sertifikasi/riwayat', [MahasiswaSertifikasiController::class, 'riwayat'])->name('sertifikasi.riwayat');
        Route::get('/sertifikasi/{pendaftaran}/cetak', [MahasiswaSertifikasiController::class, 'cetak'])->name('sertifikasi.cetak');
        Route::get('/sertifikasi/{jadwal}/daftar', [MahasiswaSertifikasiController::class, 'daftarForm'])->name('sertifikasi.daftar');
        Route::post('/sertifikasi/{jadwal}/daftar', [MahasiswaSertifikasiController::class, 'daftarStore'])->name('sertifikasi.daftar.store');
        Route::get('/izin-penelitian', [MahasiswaIzinPenelitianController::class, 'index'])->name('izin-penelitian.index');
        Route::get('/izin-penelitian/buat', [MahasiswaIzinPenelitianController::class, 'create'])->name('izin-penelitian.create');
        Route::post('/izin-penelitian/buat', [MahasiswaIzinPenelitianController::class, 'store'])->name('izin-penelitian.store');
        Route::get('/izin-penelitian/{izinPenelitian}', [MahasiswaIzinPenelitianController::class, 'show'])->name('izin-penelitian.show');
        Route::get('/izin-penelitian/{izinPenelitian}/cetak', [MahasiswaIzinPenelitianController::class, 'cetak'])->name('izin-penelitian.cetak');
        Route::get('/peminjaman-lab', [PeminjamanLabController::class, 'index'])->name('peminjaman-lab.index');
        Route::get('/peminjaman-lab/buat', [PeminjamanLabController::class, 'create'])->name('peminjaman-lab.create');
        Route::post('/peminjaman-lab/buat', [PeminjamanLabController::class, 'store'])->name('peminjaman-lab.store');
        Route::get('/peminjaman-lab/{peminjaman}', [PeminjamanLabController::class, 'show'])->name('peminjaman-lab.show');
        Route::get('/peminjaman-lab/{peminjaman}/cetak-bukti-pinjam', [PeminjamanLabController::class, 'cetakBuktiPinjam'])->name('peminjaman-lab.cetak-bukti-pinjam');
        Route::get('/peminjaman-lab/{peminjaman}/cetak-bukti-kembali', [PeminjamanLabController::class, 'cetakBuktiKembali'])->name('peminjaman-lab.cetak-bukti-kembali');
        Route::post('/peminjaman-lab/{peminjaman}/kembalikan', [PeminjamanLabController::class, 'kembalikan'])->name('peminjaman-lab.kembalikan');
        Route::get('/surat-bebas-lab', [MahasiswaSuratBebasLabController::class, 'index'])->name('surat-bebas-lab.index');
        Route::post('/surat-bebas-lab/ajukan', [MahasiswaSuratBebasLabController::class, 'store'])->name('surat-bebas-lab.store');
        Route::get('/surat-bebas-lab/{surat}', [MahasiswaSuratBebasLabController::class, 'show'])->name('surat-bebas-lab.show');
        Route::get('/surat-bebas-lab/{surat}/cetak', [MahasiswaSuratBebasLabController::class, 'cetak'])->name('surat-bebas-lab.cetak');
    });
});

// ─── DOSEN ───────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'role:dosen'])->prefix('dosen')->name('dosen.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DosenDashboardController::class, 'index'])
        ->name('dashboard');
    Route::get('/peminjaman-lab', [DosenPeminjamanLabController::class, 'index'])->name('peminjaman-lab.index');
    Route::get('/peminjaman-lab/buat', [DosenPeminjamanLabController::class, 'create'])->name('peminjaman-lab.create');
    Route::post('/peminjaman-lab', [DosenPeminjamanLabController::class, 'store'])->name('peminjaman-lab.store');
    Route::get('/peminjaman-lab/{peminjaman}', [DosenPeminjamanLabController::class, 'show'])->name('peminjaman-lab.show');
    Route::patch('/peminjaman-lab/{peminjaman}/kembalikan', [DosenPeminjamanLabController::class, 'kembalikan'])->name('peminjaman-lab.kembalikan');
    Route::get('/modul', [DosenModulController::class, 'index'])->name('modul.index');
    Route::post('/modul', [DosenModulController::class, 'store'])->name('modul.store');
    Route::delete('/modul/{modul}', [DosenModulController::class, 'destroy'])->name('modul.destroy');
    Route::get('/izin-penelitian', fn() => Inertia::render('ComingSoon', ['title' => 'Izin Penelitian', 'role' => 'dosen']))->name('izin-penelitian.index');
});

// ─── ADMIN ───────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\AdminDashboardController::class, 'index'])
        ->name('dashboard');
    Route::get('/sertifikasi', [AdminSertifikasiController::class, 'index'])->name('sertifikasi.index');
    Route::get('/sertifikasi/{pendaftaran}', [AdminSertifikasiController::class, 'show'])->name('sertifikasi.show');
    Route::patch('/sertifikasi/{pendaftaran}/verifikasi', [AdminSertifikasiController::class, 'verifikasi'])->name('sertifikasi.verifikasi');
    Route::post('/sertifikasi/{pendaftaran}/lulus', [AdminSertifikasiController::class, 'hasilLulus'])->name('sertifikasi.lulus');
    Route::post('/sertifikasi/{pendaftaran}/tidak-lulus', [AdminSertifikasiController::class, 'hasilTidakLulus'])->name('sertifikasi.tidak-lulus');
    Route::get('/skema', [AdminSkemaController::class, 'index'])->name('skema.index');
    Route::get('/skema/buat', [AdminSkemaController::class, 'create'])->name('skema.create');
    Route::post('/skema', [AdminSkemaController::class, 'store'])->name('skema.store');
    Route::get('/skema/{skema}/edit', [AdminSkemaController::class, 'edit'])->name('skema.edit');
    Route::put('/skema/{skema}', [AdminSkemaController::class, 'update'])->name('skema.update');
    Route::delete('/skema/{skema}', [AdminSkemaController::class, 'destroy'])->name('skema.destroy');
    Route::get('/jadwal', [AdminJadwalController::class, 'index'])->name('jadwal.index');
    Route::get('/jadwal/buat', [AdminJadwalController::class, 'create'])->name('jadwal.create');
    Route::post('/jadwal', [AdminJadwalController::class, 'store'])->name('jadwal.store');
    Route::get('/jadwal/{jadwal}/edit', [AdminJadwalController::class, 'edit'])->name('jadwal.edit');
    Route::put('/jadwal/{jadwal}', [AdminJadwalController::class, 'update'])->name('jadwal.update');
    Route::delete('/jadwal/{jadwal}', [AdminJadwalController::class, 'destroy'])->name('jadwal.destroy');
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    Route::get('/site-setting', [AdminSiteSettingController::class, 'index'])->name('site-setting');
    Route::put('/site-setting', [AdminSiteSettingController::class, 'update'])->name('site-setting.update');
    Route::get('/peminjaman-lab', [AdminPeminjamanLabController::class, 'index'])->name('peminjaman-lab.index');
    Route::get('/peminjaman-lab/{peminjaman}', [AdminPeminjamanLabController::class, 'show'])->name('peminjaman-lab.show');
    Route::post('/peminjaman-lab/{peminjaman}/verifikasi', [AdminPeminjamanLabController::class, 'verifikasi'])->name('peminjaman-lab.verifikasi');
    Route::post('/peminjaman-lab/{peminjaman}/tolak', [AdminPeminjamanLabController::class, 'tolak'])->name('peminjaman-lab.tolak');
    Route::post('/peminjaman-lab/{peminjaman}/verifikasi-kembali', [AdminPeminjamanLabController::class, 'verifikasiKembali'])->name('peminjaman-lab.verifikasi-kembali');
    Route::get('/izin-penelitian', [AdminIzinPenelitianController::class, 'index'])->name('izin-penelitian.index');
    Route::get('/izin-penelitian/{izinPenelitian}', [AdminIzinPenelitianController::class, 'show'])->name('izin-penelitian.show');
    Route::post('/izin-penelitian/{izinPenelitian}/verifikasi', [AdminIzinPenelitianController::class, 'verifikasi'])->name('izin-penelitian.verifikasi');
    Route::post('/izin-penelitian/{izinPenelitian}/tolak', [AdminIzinPenelitianController::class, 'tolak'])->name('izin-penelitian.tolak');
    Route::get('/surat-bebas-lab', [AdminSuratBebasLabController::class, 'index'])->name('surat-bebas-lab.index');
    Route::get('/surat-bebas-lab/{surat}', [AdminSuratBebasLabController::class, 'show'])->name('surat-bebas-lab.show');
    Route::post('/surat-bebas-lab/{surat}/verifikasi', [AdminSuratBebasLabController::class, 'verifikasi'])->name('surat-bebas-lab.verifikasi');
    Route::post('/surat-bebas-lab/{surat}/tanggungan', [AdminSuratBebasLabController::class, 'tandaiTanggungan'])->name('surat-bebas-lab.tanggungan');

    Route::get('/ruangan', [AdminRuanganController::class, 'index'])->name('ruangan.index');
    Route::post('/ruangan', [AdminRuanganController::class, 'store'])->name('ruangan.store');
    Route::put('/ruangan/{ruangan}', [AdminRuanganController::class, 'update'])->name('ruangan.update');
    Route::delete('/ruangan/{ruangan}', [AdminRuanganController::class, 'destroy'])->name('ruangan.destroy');

    Route::get('/program-studi', [AdminProgramStudiController::class, 'index'])->name('program-studi.index');
    Route::post('/program-studi', [AdminProgramStudiController::class, 'store'])->name('program-studi.store');
    Route::put('/program-studi/{program_studi}', [AdminProgramStudiController::class, 'update'])->name('program-studi.update');
    Route::delete('/program-studi/{program_studi}', [AdminProgramStudiController::class, 'destroy'])->name('program-studi.destroy');
});

// ─── BERITA (admin + kepala_lab) ─────────────────────────────
Route::middleware(['auth', 'verified', 'role:admin,kepala_lab'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('/berita', AdminBeritaController::class)->parameters([
        'berita' => 'berita'
    ]);
});

// ─── STRUKTUR ORGANISASI (admin + kepala_lab) ────────────────
Route::middleware(['auth', 'verified', 'role:admin,kepala_lab'])->prefix('kepala_lab')->name('kepala_lab.')->group(function () {
    Route::get('/struktur', [KepalaLabStrukturController::class, 'index'])->name('struktur.index');
    Route::post('/struktur', [KepalaLabStrukturController::class, 'store'])->name('struktur.store');
    Route::put('/struktur/{struktur}', [KepalaLabStrukturController::class, 'update'])->name('struktur.update');
    Route::delete('/struktur/{struktur}', [KepalaLabStrukturController::class, 'destroy'])->name('struktur.destroy');
});

// ─── KEPALA LAB ──────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'role:kepala_lab'])->prefix('kepala_lab')->name('kepala_lab.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\KepalaLabDashboardController::class, 'index'])
        ->name('dashboard');
    Route::get('/sertifikasi', [KepalaLabSertifikasiController::class, 'index'])->name('sertifikasi.index');
    Route::get('/sertifikasi/{pendaftaran}', [KepalaLabSertifikasiController::class, 'show'])->name('sertifikasi.show');
    Route::patch('/sertifikasi/{pendaftaran}/nilai', [KepalaLabSertifikasiController::class, 'nilai'])->name('sertifikasi.nilai');
    Route::get('/izin-penelitian', [KepalaLabIzinPenelitianController::class, 'index'])->name('izin-penelitian.index');
    Route::get('/izin-penelitian/{izin}', [KepalaLabIzinPenelitianController::class, 'show'])->name('izin-penelitian.show');
    Route::post('/izin-penelitian/{izin}/setujui', [KepalaLabIzinPenelitianController::class, 'setujui'])->name('izin-penelitian.setujui');
    Route::post('/izin-penelitian/{izin}/tolak', [KepalaLabIzinPenelitianController::class, 'tolak'])->name('izin-penelitian.tolak');
    Route::get('/peminjaman-lab', [KepalaLabPeminjamanLabController::class, 'index'])->name('peminjaman-lab.index');
    Route::get('/peminjaman-lab/{peminjaman}', [KepalaLabPeminjamanLabController::class, 'show'])->name('peminjaman-lab.show');
    Route::post('/peminjaman-lab/{peminjaman}/setujui', [KepalaLabPeminjamanLabController::class, 'setujui'])->name('peminjaman-lab.setujui');
    Route::post('/peminjaman-lab/{peminjaman}/tolak', [KepalaLabPeminjamanLabController::class, 'tolak'])->name('peminjaman-lab.tolak');
    Route::get('/surat-bebas-lab', [KepalaLabSuratBebasLabController::class, 'index'])->name('surat-bebas-lab.index');
    Route::get('/surat-bebas-lab/{surat}', [KepalaLabSuratBebasLabController::class, 'show'])->name('surat-bebas-lab.show');
    Route::post('/surat-bebas-lab/{surat}/setujui', [KepalaLabSuratBebasLabController::class, 'setujui'])->name('surat-bebas-lab.setujui');
    Route::get('/surat-bebas-lab/{surat}/cetak', [KepalaLabSuratBebasLabController::class, 'cetak'])->name('surat-bebas-lab.cetak');
});

// ─── PROFILE (all authenticated) ─────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/api/peminjaman-lab/cek-jadwal', [PeminjamanLabController::class, 'cekJadwal'])->name('api.peminjaman-lab.cek-jadwal');
});

require __DIR__ . '/auth.php';

// ─── INERTIA DEMO (isolated – does not affect SPA) ───────────
// Blade: resources/views/inertia-app.blade.php
// Page:  resources/js/Pages/InertiaDemo.vue
Route::get('/inertia-demo', [InertiaDemoController::class, 'index'])
    ->middleware(['web'])
    ->name('inertia.demo');

// ─── FALLBACK ROUTE ─────────────────────────────────────
Route::fallback(function () {
    return redirect('/');
});
