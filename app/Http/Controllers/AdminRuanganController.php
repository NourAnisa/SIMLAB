<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Ruangan;
use Illuminate\Http\Request;

class AdminRuanganController extends Controller
{
    public function index(Request $request)
    {
        $ruangans = Ruangan::latest()->get();

        return Inertia::render('Admin/Ruangan/Index', [
            'ruangans' => $ruangans
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_ruangan' => 'required|string|max:150|unique:ruangans,nama_ruangan',
            'fasilitas' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        $ruangan = Ruangan::create($request->all());

        return back()->with('success', 'Ruangan berhasil ditambahkan.');
    }

    public function update(Request $request, Ruangan $ruangan)
    {
        $request->validate([
            'nama_ruangan' => 'required|string|max:150|unique:ruangans,nama_ruangan,' . $ruangan->id,
            'fasilitas' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        $ruangan->update($request->all());

        return back()->with('success', 'Ruangan berhasil diperbarui.');
    }

    public function destroy(Ruangan $ruangan)
    {
        $ruangan->delete();

        return back()->with('success', 'Ruangan berhasil dihapus.');
    }
}
