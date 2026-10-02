<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::latest();
        if ($request->role) $query->where('role', $request->role);
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('nim_nip', 'like', '%' . $request->search . '%');
            });
        }
        
        $users = $query->paginate(15);
        
        $stats = [
            'total' => User::count(),
            'mahasiswa' => User::where('role', 'mahasiswa')->count(),
            'dosen' => User::where('role', 'dosen')->count(),
            'admin_lab' => User::whereIn('role', ['admin', 'kepala_lab'])->count(),
        ];
        return Inertia::render('Admin/User/Index', [
            'users' => $users,
            'stats' => $stats,
            'filters' => request()->only(['search', 'role']),
            'programStudis' => \App\Models\ProgramStudi::orderBy('jenjang')->orderBy('nama_program_studi')->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|max:100',
            'email'    => [
                'required', 'email', 'unique:users,email',
                new \App\Rules\DomainEmailRule($request->role)
            ],
            'role'     => 'required|in:mahasiswa,admin,kepala_lab,dosen',
            'nim_nip'  => 'nullable|max:30',
            'jurusan'  => 'nullable|max:100',
            'password' => 'required|min:8|confirmed',
        ]);
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'role'     => $request->role,
            'nim_nip'  => $request->nim_nip,
            'jurusan'  => $request->jurusan,
            'password' => Hash::make($request->password),
            'email_verified_at' => now(),
        ]);
        
        return back()->with('success', 'User berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'    => 'required|max:100',
            'email'   => [
                'required', 'email', 'unique:users,email,' . $user->id,
                new \App\Rules\DomainEmailRule($request->role)
            ],
            'role'    => 'required|in:mahasiswa,admin,kepala_lab,dosen',
            'nim_nip' => 'nullable|max:30',
            'jurusan' => 'nullable|max:100',
            'password' => 'nullable|min:8|confirmed',
        ]);
        $data = $request->only('name', 'email', 'role', 'nim_nip', 'jurusan');
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        $user->update($data);
        
        return back()->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menghapus akun sendiri.');
        }
        try {
            $user->delete();
            return back()->with('success', 'User berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->with('error', 'User tidak dapat dihapus karena masih memiliki data terkait (jadwal, peminjaman, dll).');
        }
    }
}
