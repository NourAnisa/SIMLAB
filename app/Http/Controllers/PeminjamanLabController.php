<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\PeminjamanLab;
use App\AvailableSchedule;
use Illuminate\Http\Request;

class PeminjamanLabController extends Controller
{
    public function index(Request $request)
    {
        $peminjamans = PeminjamanLab::where('user_id', auth()->id())->latest()->paginate(10);
        return Inertia::render('Mahasiswa/PeminjamanLab/Index', [
            'peminjamans' => $peminjamans
        ]);
    }

    public function create()
    {
        $ruangans = \App\Models\Ruangan::all();
        return Inertia::render('Mahasiswa/PeminjamanLab/Create', [
            'ruangans' => $ruangans
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'ruangan'          => ['required', 'max:100', new \App\AvailableSchedule(
                $request->ruangan,
                $request->tanggal_pinjam,
                $request->jam_mulai,
                $request->jam_selesai
            )],
            'tanggal_pinjam'   => 'required|date|after_or_equal:today',
            'jam_mulai'        => 'required',
            'jam_selesai'      => 'required|after:jam_mulai',
            'keperluan'        => 'required',
            'surat_keterangan' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $path = null;
        if ($request->hasFile('surat_keterangan')) {
            $path = $request->file('surat_keterangan')->store('peminjaman', 'public');
        }

        $peminjaman = PeminjamanLab::create([
            'user_id'          => auth()->id(),
            'ruangan'          => $request->ruangan,
            'tanggal_pinjam'   => $request->tanggal_pinjam,
            'jam_mulai'        => $request->jam_mulai,
            'jam_selesai'      => $request->jam_selesai,
            'keperluan'        => $request->keperluan,
            'surat_keterangan' => $path,
            'status'           => 'menunggu',
        ]);



        return redirect()->route('mahasiswa.peminjaman-lab.index')->with('success', 'Permohonan peminjaman berhasil diajukan.');
    }

    public function show(Request $request, PeminjamanLab $peminjaman)
    {
        abort_if($peminjaman->user_id !== auth()->id(), 403);
        return Inertia::render('Mahasiswa/PeminjamanLab/Show', [
            'peminjaman' => $peminjaman
        ]);
    }

    /**
     * Cetak bukti peminjaman lab
     */
    public function cetakBuktiPinjam(Request $request, PeminjamanLab $peminjaman)
    {
        abort_if($peminjaman->user_id !== auth()->id(), 403);

        $kepalaLab = \App\Models\User::where('role', 'kepala_lab')->first();

        // wantsJson removed

        return view('mahasiswa.peminjaman_lab.cetak-bukti-pinjam', [
            'peminjaman' => $peminjaman,
            'kepalaLab' => $kepalaLab,
        ]);
    }

    /**
     * Cetak bukti pengembalian lab
     */
    public function cetakBuktiKembali(Request $request, PeminjamanLab $peminjaman)
    {
        abort_if($peminjaman->user_id !== auth()->id(), 403);

        $kepalaLab = \App\Models\User::where('role', 'kepala_lab')->first();

        // wantsJson removed

        return view('mahasiswa.peminjaman_lab.cetak-bukti-kembali', [
            'peminjaman' => $peminjaman,
            'kepalaLab' => $kepalaLab,
        ]);
    }

    public function kembalikan(Request $request, PeminjamanLab $peminjaman)
    {
        abort_if($peminjaman->user_id !== auth()->id(), 403);
        abort_if($peminjaman->status !== 'disetujui', 403);
        $peminjaman->update([
            'status' => 'menunggu_kembali',
            'waktu_dikembalikan' => now()
        ]);



        return back()->with('success', 'Pengembalian ruangan berhasil dilaporkan. Menunggu verifikasi admin.');
    }

    public function cekJadwal(Request $request)
    {
        $ruangan = $request->query('ruangan');
        $tanggal = $request->query('tanggal');

        if (!$ruangan || !$tanggal) {
            return response()->json([]);
        }

        $jadwalTerpakai = PeminjamanLab::with('user:id,name')
            ->where('ruangan', $ruangan)
            ->where('tanggal_pinjam', $tanggal)
            ->whereIn('status', ['menunggu', 'diverifikasi', 'disetujui', 'menunggu_kembali'])
            ->orderBy('jam_mulai')
            ->get(['id', 'jam_mulai', 'jam_selesai', 'status', 'user_id', 'keperluan']);

        return response()->json($jadwalTerpakai);
    }
}
