<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Http\Requests\ProfileUpdateRequest;
use App\ProfileValidationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(
        private ProfileValidationService $validationService
    ) {}

    public function edit(Request $request)
    {
        $user = $request->user();

        // Get validation status untuk ditampilkan di view
        $profileValidation = $this->validationService->validateProfileCompletion($user);

        return Inertia::render('Profile/Edit', [
            'user' => $user,
            'validation' => $profileValidation,
            'programStudis' => \App\Models\ProgramStudi::orderBy('jenjang')->orderBy('nama_program_studi')->get(),
        ]);
    }

    public function update(ProfileUpdateRequest $request)
    {
        $user = $request->user();
        $data = $request->only(['name', 'email', 'nim_nip', 'jurusan']);
        $emailChanged = array_key_exists('email', $data) && $data['email'] !== $user->email;

        if ($emailChanged) {
            $user->email_verified_at = null;
        }

        // Upload foto profil
        if ($request->hasFile('foto_profil')) {
            if ($user->foto_profil) {
                Storage::disk('public')->delete($user->foto_profil);
            }
            $data['foto_profil'] = $request->file('foto_profil')->store('foto_profil', 'public');
        }

        // Upload tanda tangan (khusus kepala_lab)
        if ($request->hasFile('tanda_tangan') && $user->role === 'kepala_lab') {
            if ($user->tanda_tangan) {
                Storage::disk('public')->delete($user->tanda_tangan);
            }
            $data['tanda_tangan'] = $request->file('tanda_tangan')->store('tanda_tangan', 'public');
        }

        $user->fill($data);
        $user->save();

        // Auto-check dan update profile_completed flag untuk mahasiswa
        if ($user->isMahasiswa()) {
            $this->validationService->checkAndUpdateProfileCompletion($user);
        }

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function destroy(Request $request)
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        Auth::logout();
        $user->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // wantsJson removed

        return Redirect::to('/');
    }
}
