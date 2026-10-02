<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Berita;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class AdminBeritaController extends Controller
{
    public function index(Request $request)
    {
        $query = Berita::with('user')->latest();
        if ($search = $request->search) {
            $query->where('judul', 'like', "%$search%");
        }
        if ($kat = $request->kategori) {
            $query->where('kategori', $kat);
        }
        
        $beritas = $query->paginate(15);
        $kategoriList = Berita::distinct('kategori')->pluck('kategori')->filter();
        
        return Inertia::render('Admin/Berita/Index', [
            'beritas' => $beritas,
            'kategoris' => $kategoriList,
            'filters' => request()->only(['search', 'kategori'])
        ]);
    }

    public function create()
    {
        return redirect()->route('admin.berita.index');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'judul'     => 'required|max:200',
            'kategori'  => 'nullable|max:50',
            'ringkasan' => 'nullable|max:255',
            'konten'    => 'required',
            'gambar'    => 'nullable|image|max:2048',
        ]);
        $data['user_id'] = auth()->id();
        $data['slug'] = \Illuminate\Support\Str::slug($data['judul']) . '-' . time();
        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('berita', 'public');
        }
        
        $berita = Berita::create($data);
        
        return redirect()->route('admin.berita.index')->with('success', 'Berita berhasil ditambahkan.');
    }

    public function show(Berita $berita)
    {
        return redirect()->route('admin.berita.index');
    }

    public function edit(Berita $berita)
    {
        return redirect()->route('admin.berita.index');
    }

    public function update(Request $request, Berita $berita)
    {
        $data = $request->validate([
            'judul'     => 'required|max:200',
            'kategori'  => 'nullable|max:50',
            'ringkasan' => 'nullable|max:255',
            'konten'    => 'required',
            'gambar'    => 'nullable|image|max:2048',
        ]);
        if ($request->hasFile('gambar')) {
            if ($berita->gambar) \Illuminate\Support\Facades\Storage::disk('public')->delete($berita->gambar);
            $data['gambar'] = $request->file('gambar')->store('berita', 'public');
        }
        $berita->update($data);
        
        return redirect()->route('admin.berita.index')->with('success', 'Berita berhasil diperbarui.');
    }

    public function destroy(Request $request, Berita $berita)
    {
        if ($berita->gambar) \Illuminate\Support\Facades\Storage::disk('public')->delete($berita->gambar);
        $berita->delete();
        
        return redirect()->route('admin.berita.index')->with('success', 'Berita berhasil dihapus.');
    }
}
