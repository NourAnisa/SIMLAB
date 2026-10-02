<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\IzinPenelitian;
use App\Models\User;
use Illuminate\Http\Request;

class MahasiswaIzinPenelitianController extends Controller
{
    public function index(Request $request)
    {
        $izins = IzinPenelitian::where('user_id', auth()->id())->latest()->paginate(10);
        return Inertia::render('Mahasiswa/IzinPenelitian/Index', [
            'izins' => $izins
        ]);
    }

    public function create()
    {
        return Inertia::render('Mahasiswa/IzinPenelitian/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul_penelitian' => 'required|max:255',
            'tujuan'           => 'required',
            'dokumen_berkas'   => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'data_dibutuhkan'  => 'nullable|array',
            'data_dibutuhkan.*'=> 'string|max:200',
        ]);
        $path = null;
        if ($request->hasFile('dokumen_berkas')) {
            $path = $request->file('dokumen_berkas')->store('izin_penelitian', 'public');
        }
        $izin = IzinPenelitian::create([
            'user_id'          => auth()->id(),
            'judul_penelitian' => $request->judul_penelitian,
            'tujuan'           => $request->tujuan,
            'dokumen_berkas'   => $path,
            'data_dibutuhkan'  => $request->data_dibutuhkan ?? [],
            'status'           => 'menunggu',
        ]);
        

        
        return redirect()->route('mahasiswa.izin-penelitian.index')
            ->with('success', 'Permohonan izin penelitian berhasil diajukan. Kami akan memproses permohonan Anda segera.');
    }

    public function show(Request $request, IzinPenelitian $izinPenelitian)
    {
        abort_if($izinPenelitian->user_id !== auth()->id(), 403);
        return Inertia::render('Mahasiswa/IzinPenelitian/Show', [
            'izin' => $izinPenelitian
        ]);
    }

    public function cetak(IzinPenelitian $izinPenelitian)
    {
        abort_if($izinPenelitian->user_id !== auth()->id(), 403);
        abort_if($izinPenelitian->status !== 'disetujui', 403, 'Surat hanya bisa dicetak jika sudah disetujui.');
        $izinPenelitian->load('user');
        $kepalaLab = User::where('role', 'kepala_lab')->first();
        return view('mahasiswa.izin-penelitian.cetak', [
            'izin'      => $izinPenelitian,
            'kepalaLab' => $kepalaLab,
        ]);
    }
}
