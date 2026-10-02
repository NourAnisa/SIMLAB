<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Modul;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DosenModulController extends Controller
{
    public function index(Request $request)
    {
        $moduls = Modul::where('user_id', auth()->id())->latest()->paginate(12);
        return Inertia::render('Dosen/Modul/Index', [
            'moduls' => $moduls
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_modul'  => 'required|max:200',
            'mata_kuliah' => 'required|max:150',
            'deskripsi'   => 'nullable|max:500',
            'file'        => 'required|file|mimes:pdf,doc,docx,ppt,pptx|max:10240',
        ]);

        $path = $request->file('file')->store('moduls', 'public');

        Modul::create([
            'user_id'     => auth()->id(),
            'nama_modul'  => $request->nama_modul,
            'mata_kuliah' => $request->mata_kuliah,
            'deskripsi'   => $request->deskripsi,
            'file_path'   => $path,
        ]);

        // wantsJson removed

        return back()->with('success', 'Modul berhasil diunggah.');
    }

    public function destroy(Request $request, Modul $modul)
    {
        abort_if($modul->user_id !== auth()->id(), 403);
        Storage::disk('public')->delete($modul->file_path);
        $modul->delete();
        
        // wantsJson removed

        return back()->with('success', 'Modul berhasil dihapus.');
    }
}
