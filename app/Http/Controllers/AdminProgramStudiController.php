<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;

class AdminProgramStudiController extends Controller
{
    public function index(Request $request)
    {
        $programStudis = ProgramStudi::orderBy('jenjang')->orderBy('nama_program_studi')->get();

        return Inertia::render('Admin/ProgramStudi/Index', [
            'programStudis' => $programStudis
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_program_studi' => 'required|string|max:150|unique:program_studis,nama_program_studi',
            'jenjang' => 'nullable|string|max:50',
        ]);

        $ps = ProgramStudi::create($request->all());

        return back()->with('success', 'Program Studi berhasil ditambahkan.');
    }

    public function update(Request $request, ProgramStudi $program_studi)
    {
        $request->validate([
            'nama_program_studi' => 'required|string|max:150|unique:program_studis,nama_program_studi,' . $program_studi->id,
            'jenjang' => 'nullable|string|max:50',
        ]);

        $program_studi->update($request->all());

        return back()->with('success', 'Program Studi berhasil diperbarui.');
    }

    public function destroy(ProgramStudi $program_studi)
    {
        $program_studi->delete();

        return back()->with('success', 'Program Studi berhasil dihapus.');
    }
}
