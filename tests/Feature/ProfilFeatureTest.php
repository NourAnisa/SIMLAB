<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Feature: Profil — edit dan update profil untuk semua role.
 * Mencakup: edit profil, update (nama, foto, tanda tangan), hapus akun,
 * dan auto-update profile_completed setelah profil mahasiswa dilengkapi.
 */
class ProfilFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
        Storage::fake('public');
    }

    // ── Akses Halaman Edit ────────────────────────────────────────

    public function test_semua_role_bisa_akses_halaman_edit_profil(): void
    {
        $roles = ['mahasiswa', 'dosen', 'admin', 'kepala_lab'];

        foreach ($roles as $role) {
            /** @var User $user */
            $user = User::factory()->create([
                'role'              => $role,
                'profile_completed' => true,
            ]);
            $this->actingAs($user);

            $this->get(route('profile.edit'))->assertOk();
        }
    }

    public function test_tamu_diredirect_ke_login(): void
    {
        $this->get(route('profile.edit'))->assertRedirect(route('login'));
    }

    // ── Update Nama ───────────────────────────────────────────────

    public function test_user_berhasil_update_nama(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'dosen']);
        $this->actingAs($user);

        $response = $this->patch(route('profile.update'), [
            'name'  => 'Nama Baru',
            'email' => $user->email,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id'   => $user->id,
            'name' => 'Nama Baru',
        ]);
    }

    // ── Update Foto Profil ────────────────────────────────────────

    public function test_user_berhasil_upload_foto_profil(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'mahasiswa', 'profile_completed' => true]);
        $this->actingAs($user);

        $response = $this->patch(route('profile.update'), [
            'name'        => $user->name,
            'email'       => $user->email,
            'foto_profil' => UploadedFile::fake()->image('foto.jpg', 200, 200),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseMissing('users', [
            'id'          => $user->id,
            'foto_profil' => null,
        ]);
    }

    // ── Update Tanda Tangan (hanya kepala_lab) ────────────────────

    public function test_kepala_lab_berhasil_upload_tanda_tangan(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'kepala_lab']);
        $this->actingAs($user);

        $response = $this->patch(route('profile.update'), [
            'name'          => $user->name,
            'email'         => $user->email,
            'tanda_tangan'  => UploadedFile::fake()->image('ttd.png', 150, 60),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseMissing('users', [
            'id'           => $user->id,
            'tanda_tangan' => null,
        ]);
    }

    // ── Validasi Update ───────────────────────────────────────────

    public function test_update_gagal_tanpa_nama(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'dosen']);
        $this->actingAs($user);

        $this->patch(route('profile.update'), [
            'email' => $user->email,
        ])->assertSessionHasErrors(['name']);
    }

    public function test_update_gagal_tanpa_email(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'dosen']);
        $this->actingAs($user);

        $this->patch(route('profile.update'), [
            'name' => 'Nama',
        ])->assertSessionHasErrors(['email']);
    }

    // ── profile_completed Auto-Update (Mahasiswa) ─────────────────

    public function test_profile_completed_diupdate_setelah_lengkapi_profil_mahasiswa(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'role'              => 'mahasiswa',
            'profile_completed' => false,
            'nim_nip'           => null,
            'jurusan'           => null,
            'foto_profil'       => null,
        ]);
        $this->actingAs($user);

        $this->patch(route('profile.update'), [
            'name'        => 'Mahasiswa Lengkap',
            'email'       => $user->email,
            'nim_nip'     => '20210099',
            'jurusan'     => 'Teknik Informatika',
            'foto_profil' => UploadedFile::fake()->image('foto.jpg', 200, 200),
        ]);

        $this->assertDatabaseHas('users', [
            'id'                => $user->id,
            'profile_completed' => true,
        ]);
    }

    // ── Hapus Akun ────────────────────────────────────────────────

    public function test_user_berhasil_hapus_akun_dengan_password_benar(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'role'     => 'dosen',
            'password' => bcrypt('password'),
        ]);
        $this->actingAs($user);

        $response = $this->delete(route('profile.destroy'), [
            'password' => 'password',
        ]);

        $response->assertRedirect('/');
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_hapus_akun_gagal_dengan_password_salah(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'role'     => 'dosen',
            'password' => bcrypt('password'),
        ]);
        $this->actingAs($user);

        $this->delete(route('profile.destroy'), [
            'password' => 'wrong-password',
        ])->assertSessionHasErrors(['password'], null, 'userDeletion');
    }
}
