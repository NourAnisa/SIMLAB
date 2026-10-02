<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\SkemaSertifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminSkemaController extends Controller
{
    public function index(Request $request)
    {
        $skemas = SkemaSertifikasi::withCount('jadwals')->get();
        return Inertia::render('Admin/Skema/Index', [
            'skemas' => $skemas
        ]);
    }

    public function create()
    {
        return redirect()->route('admin.skema.index');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_skema' => 'required|max:100',
            'deskripsi'  => 'required',
            'logo'       => 'nullable|image|mimes:jpg,jpeg,png,svg,webp|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('skema_logos', 'public');
        }

        $skema = SkemaSertifikasi::create($data);
        
        return redirect()->route('admin.skema.index')->with('success', 'Skema berhasil ditambahkan.');
    }

    public function show(Request $request, SkemaSertifikasi $skema)
    {
        return redirect()->route('admin.skema.index');
    }

    public function edit(Request $request, SkemaSertifikasi $skema)
    {
        return redirect()->route('admin.skema.index');
    }

    public function update(Request $request, SkemaSertifikasi $skema)
    {
        $data = $request->validate([
            'nama_skema' => 'required|max:100',
            'deskripsi'  => 'required',
            'logo'       => 'nullable|image|mimes:jpg,jpeg,png,svg,webp|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            if ($skema->logo) Storage::disk('public')->delete($skema->logo);
            $data['logo'] = $request->file('logo')->store('skema_logos', 'public');
        }

        $skema->update($data);
        
        return redirect()->route('admin.skema.index')->with('success', 'Skema berhasil diperbarui.');
    }

    public function destroy(Request $request, SkemaSertifikasi $skema)
    {
        if ($skema->logo) Storage::disk('public')->delete($skema->logo);
        $skema->delete();
        
        return redirect()->route('admin.skema.index')->with('success', 'Skema berhasil dihapus.');
    }
}
