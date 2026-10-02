<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\StrukturOrganisasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KepalaLabStrukturController extends Controller
{
    public function index(Request $request)
    {
        $strukturs = StrukturOrganisasi::orderBy('urutan')->orderBy('id')->get();
        return Inertia::render('KepalaLab/Struktur/Index', [
            'strukturs' => $strukturs
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama'      => 'required|string|max:255',
            'jabatan'   => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'foto'      => 'nullable|image|max:2048',
            'urutan'    => 'nullable|integer|min:0',
        ]);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('struktur', 'public');
        }

        $struktur = StrukturOrganisasi::create($data);



        return back()->with('success', 'Anggota berhasil ditambahkan.');
    }

    public function update(Request $request, StrukturOrganisasi $struktur)
    {
        $data = $request->validate([
            'nama'      => 'required|string|max:255',
            'jabatan'   => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'foto'      => 'nullable|image|max:2048',
            'urutan'    => 'nullable|integer|min:0',
        ]);

        if ($request->hasFile('foto')) {
            if ($struktur->foto) {
                Storage::disk('public')->delete($struktur->foto);
            }
            $data['foto'] = $request->file('foto')->store('struktur', 'public');
        }

        $struktur->update($data);



        return back()->with('success', 'Data berhasil diperbarui.');
    }

    public function destroy(Request $request, StrukturOrganisasi $struktur)
    {
        if ($struktur->foto) {
            Storage::disk('public')->delete($struktur->foto);
        }
        $struktur->delete();



        return back()->with('success', 'Anggota berhasil dihapus.');
    }
}
