<?php
namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\JadwalSertifikasi;
use App\Models\SkemaSertifikasi;
use Illuminate\Http\Request;

class AdminJadwalController extends Controller
{
    public function index(Request $request)
    {
        $jadwals = JadwalSertifikasi::with('skema')->withCount('pendaftarans')->latest()->paginate(10);
        
        $skemas = SkemaSertifikasi::all();
        return Inertia::render('Admin/Jadwal/Index', [
            'jadwals' => $jadwals,
            'skemas' => $skemas
        ]);
    }

    public function create()
    {
        return redirect()->route('admin.jadwal.index');
    }

    public function store(Request $request)
    {
        $request->validate(['skema_id' => 'required|exists:skema_sertifikasi,id', 'tanggal' => 'required|date', 'kuota' => 'required|integer|min:1']);
        $jadwal = JadwalSertifikasi::create($request->only('skema_id', 'tanggal', 'kuota'));
        
        return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function show(Request $request, JadwalSertifikasi $jadwal)
    {
        return redirect()->route('admin.jadwal.index');
    }

    public function edit(Request $request, JadwalSertifikasi $jadwal)
    {
        return redirect()->route('admin.jadwal.index');
    }

    public function update(Request $request, JadwalSertifikasi $jadwal)
    {
        $request->validate(['skema_id' => 'required|exists:skema_sertifikasi,id', 'tanggal' => 'required|date', 'kuota' => 'required|integer|min:1']);
        $jadwal->update($request->only('skema_id', 'tanggal', 'kuota'));
        
        return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(Request $request, JadwalSertifikasi $jadwal)
    {
        $jadwal->delete();
        
        return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal berhasil dihapus.');
    }
}
