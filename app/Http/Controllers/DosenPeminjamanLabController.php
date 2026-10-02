<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\PeminjamanLab;
use Illuminate\Http\Request;

class DosenPeminjamanLabController extends Controller
{
    public function index(Request $request)
    {
        $peminjamans = PeminjamanLab::where('user_id', auth()->id())->latest()->paginate(10);
        $ruangans = \App\Models\Ruangan::orderBy('nama_ruangan')->get();
        return Inertia::render('Dosen/PeminjamanLab/Index', [
            'peminjamans' => $peminjamans,
            'ruangans' => $ruangans,
        ]);
    }

    public function create()
    {
        $ruangans = \App\Models\Ruangan::orderBy('nama_ruangan')->get();
        return Inertia::render('Dosen/PeminjamanLab/Create', [
            'ruangans' => $ruangans,
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
        
        return redirect()->route('dosen.peminjaman-lab.index')->with('success', 'Permohonan peminjaman berhasil diajukan.');
    }

    public function show(Request $request, PeminjamanLab $peminjaman)
    {
        abort_if($peminjaman->user_id !== auth()->id(), 403);
        return Inertia::render('Dosen/PeminjamanLab/Show', [
            'peminjaman' => $peminjaman
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
}
