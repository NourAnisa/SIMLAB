<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\SuratBebasLab;
use App\Models\User;
use App\SuratBebasLabService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class MahasiswaSuratBebasLabController extends Controller
{
    public function __construct(private SuratBebasLabService $service) {}

    public function index(Request $request)
    {
        $surats = SuratBebasLab::where('user_id', auth()->id())->latest()->paginate(10);
        return Inertia::render('Mahasiswa/SuratBebasLab/Index', [
            'surats' => $surats
        ]);
    }

    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        try {
            $surat = $this->service->submit($user);
        } catch (ValidationException $exception) {
            $message = collect($exception->errors())->flatten()->first()
                ?? 'Permohonan surat bebas lab gagal diajukan.';



            return redirect()
                ->route('mahasiswa.surat-bebas-lab.index')
                ->withErrors($exception->errors());
        }



        return redirect()->route('mahasiswa.surat-bebas-lab.index')->with('info', 'Permohonan surat bebas lab berhasil diajukan.');
    }

    public function show(Request $request, SuratBebasLab $surat)
    {
        abort_if($surat->user_id !== auth()->id(), 403);
        return Inertia::render('Mahasiswa/SuratBebasLab/Show', [
            'surat' => $surat
        ]);
    }

    public function cetak(SuratBebasLab $surat)
    {
        abort_if($surat->user_id !== auth()->id(), 403);
        abort_if($surat->status !== 'disetujui', 403);
        $surat->load('user');
        $kepalaLab = User::where('role', 'kepala_lab')->first();
        return view('mahasiswa.surat_bebas.cetak', compact('surat', 'kepalaLab'));
    }
}
