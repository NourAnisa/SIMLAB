<?php

namespace Tests\Feature;

use App\Models\Modul;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Feature: Modul — upload dan hapus modul oleh Dosen.
 * Dosen hanya bisa hapus modul milik sendiri.
 * Role lain diblokir dari route modul dosen.
 */
class ModulFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
        Storage::fake('public');
    }

    private function makeDosen(): User
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'dosen']);
        return $user;
    }

    // ── Akses ─────────────────────────────────────────────────────

    public function test_dosen_bisa_akses_halaman_modul(): void
    {
        $this->actingAs($this->makeDosen());

        $this->get(route('dosen.modul.index'))->assertOk();
    }

    public function test_mahasiswa_diblokir_dari_halaman_modul_dosen(): void
    {
        /** @var User $mahasiswa */
        $mahasiswa = User::factory()->create(['role' => 'mahasiswa', 'profile_completed' => true]);
        $this->actingAs($mahasiswa);

        $this->get(route('dosen.modul.index'))->assertForbidden();
    }

    public function test_admin_diblokir_dari_halaman_modul_dosen(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $this->get(route('dosen.modul.index'))->assertForbidden();
    }

    // ── Upload Modul ──────────────────────────────────────────────
 
    public function test_dosen_berhasil_upload_modul(): void
    {
        $dosen = $this->makeDosen();
        $this->actingAs($dosen);
 
        $response = $this->post(route('dosen.modul.store'), [
            'nama_modul'  => 'Modul Basis Data',
            'mata_kuliah' => 'Basis Data',
            'deskripsi'   => 'Modul pengantar basis data relasional.',
            'file'        => UploadedFile::fake()->create('modul.pdf', 500, 'application/pdf'),
        ]);
 
        $response->assertRedirect();
        $this->assertDatabaseHas('moduls', [
            'user_id'    => $dosen->id,
            'nama_modul' => 'Modul Basis Data',
        ]);
    }
 
    public function test_upload_modul_gagal_tanpa_judul(): void
    {
        $dosen = $this->makeDosen();
        $this->actingAs($dosen);
 
        $this->post(route('dosen.modul.store'), [
            'mata_kuliah' => 'Basis Data',
            'file'        => UploadedFile::fake()->create('modul.pdf', 300, 'application/pdf'),
        ])->assertSessionHasErrors(['nama_modul']);
    }
 
    public function test_upload_modul_gagal_tanpa_file(): void
    {
        $dosen = $this->makeDosen();
        $this->actingAs($dosen);
 
        $this->post(route('dosen.modul.store'), [
            'nama_modul'  => 'Modul Tanpa File',
            'mata_kuliah' => 'Basis Data',
            'deskripsi'   => 'Tidak ada file.',
        ])->assertSessionHasErrors(['file']);
    }
 
    // ── Hapus Modul (milik sendiri) ───────────────────────────────
 
    public function test_dosen_berhasil_hapus_modul_milik_sendiri(): void
    {
        $dosen = $this->makeDosen();
        $modul = Modul::create([
            'user_id'     => $dosen->id,
            'nama_modul'  => 'Modul Hapus',
            'mata_kuliah' => 'Basis Data',
            'deskripsi'   => 'Akan dihapus.',
            'file_path'   => 'moduls/dummy.pdf',
        ]);
        $this->actingAs($dosen);
 
        $response = $this->delete(route('dosen.modul.destroy', $modul));
 
        $response->assertRedirect();
        $this->assertDatabaseMissing('moduls', ['id' => $modul->id]);
    }
 
    // ── Tidak Bisa Hapus Milik Dosen Lain ────────────────────────
 
    public function test_dosen_tidak_bisa_hapus_modul_milik_dosen_lain(): void
    {
        $dosen1 = $this->makeDosen();
        $dosen2 = $this->makeDosen();
        $modul  = Modul::create([
            'user_id'     => $dosen2->id,
            'nama_modul'  => 'Modul Milik Dosen2',
            'mata_kuliah' => 'Basis Data',
            'deskripsi'   => 'Bukan milik dosen1.',
            'file_path'   => 'moduls/dummy2.pdf',
        ]);
        $this->actingAs($dosen1);
 
        $this->delete(route('dosen.modul.destroy', $modul))->assertForbidden();
        $this->assertDatabaseHas('moduls', ['id' => $modul->id]);
    }
}
