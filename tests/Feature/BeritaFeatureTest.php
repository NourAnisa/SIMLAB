<?php

namespace Tests\Feature;

use App\Models\Berita;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Feature: Berita — CRUD berita oleh Admin dan Kepala Lab.
 * Mahasiswa dan Dosen hanya bisa membaca (jika ada route publik).
 * Route berita berada di bawah middleware role:admin,kepala_lab.
 */
class BeritaFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
        Storage::fake('public');
    }

    private function makeAdmin(): User
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'admin']);
        return $user;
    }

    private function makeKepalaLab(): User
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'kepala_lab']);
        return $user;
    }

    // ── Akses ─────────────────────────────────────────────────────

    public function test_admin_bisa_akses_halaman_berita(): void
    {
        $this->actingAs($this->makeAdmin());

        $this->get(route('admin.berita.index'))->assertOk();
    }

    public function test_kepala_lab_bisa_akses_halaman_berita(): void
    {
        $this->actingAs($this->makeKepalaLab());

        $this->get(route('admin.berita.index'))->assertOk();
    }

    public function test_mahasiswa_diblokir_dari_halaman_berita(): void
    {
        /** @var User $mahasiswa */
        $mahasiswa = User::factory()->create(['role' => 'mahasiswa', 'profile_completed' => true]);
        $this->actingAs($mahasiswa);

        $this->get(route('admin.berita.index'))->assertForbidden();
    }

    public function test_dosen_diblokir_dari_halaman_berita(): void
    {
        /** @var User $dosen */
        $dosen = User::factory()->create(['role' => 'dosen']);
        $this->actingAs($dosen);

        $this->get(route('admin.berita.index'))->assertForbidden();
    }

    // ── Create ────────────────────────────────────────────────────

    public function test_admin_berhasil_buat_berita(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin);

        $response = $this->post(route('admin.berita.store'), [
            'judul'   => 'Berita Terbaru SIMLAB',
            'konten'  => 'Isi konten berita selengkapnya...',
            'gambar'  => UploadedFile::fake()->image('gambar.jpg', 800, 400),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('beritas', [
            'judul' => 'Berita Terbaru SIMLAB',
        ]);
    }

    public function test_kepala_lab_berhasil_buat_berita(): void
    {
        $kepala = $this->makeKepalaLab();
        $this->actingAs($kepala);

        $response = $this->post(route('admin.berita.store'), [
            'judul'  => 'Pengumuman Kepala Lab',
            'konten' => 'Konten pengumuman...',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('beritas', [
            'judul' => 'Pengumuman Kepala Lab',
        ]);
    }

    public function test_buat_berita_gagal_tanpa_judul(): void
    {
        $this->actingAs($this->makeAdmin());

        $this->post(route('admin.berita.store'), [
            'konten' => 'Isi konten...',
        ])->assertSessionHasErrors(['judul']);
    }

    public function test_buat_berita_gagal_tanpa_konten(): void
    {
        $this->actingAs($this->makeAdmin());

        $this->post(route('admin.berita.store'), [
            'judul' => 'Judul Berita',
        ])->assertSessionHasErrors(['konten']);
    }

    // ── Edit / Update ─────────────────────────────────────────────

    public function test_admin_berhasil_update_berita(): void
    {
        $admin  = $this->makeAdmin();
        $berita = Berita::create([
            'judul'    => 'Judul Lama',
            'slug'     => 'judul-lama',
            'konten'   => 'Konten lama.',
            'user_id'  => $admin->id,
        ]);
        $this->actingAs($admin);

        $response = $this->patch(route('admin.berita.update', $berita), [
            'judul'  => 'Judul Baru',
            'konten' => 'Konten baru yang diperbarui.',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirectToRoute('admin.berita.index');
        $this->assertDatabaseHas('beritas', [
            'id'    => $berita->id,
            'judul' => 'Judul Baru',
        ]);
    }

    // ── Delete ────────────────────────────────────────────────────

    public function test_admin_berhasil_hapus_berita(): void
    {
        $admin  = $this->makeAdmin();
        $berita = Berita::create([
            'judul'   => 'Berita Dihapus',
            'slug'    => 'berita-dihapus',
            'konten'  => 'Konten berita.',
            'user_id' => $admin->id,
        ]);
        $id = $berita->id;
        $this->actingAs($admin);

        $response = $this->delete(route('admin.berita.destroy', $berita));

        $response->assertRedirectToRoute('admin.berita.index');
        $this->assertDatabaseMissing('beritas', ['id' => $id]);
    }

    public function test_kepala_lab_berhasil_hapus_berita(): void
    {
        $kepala = $this->makeKepalaLab();
        $berita = Berita::create([
            'judul'   => 'Berita oleh Kepala Lab',
            'slug'    => 'berita-oleh-kepala-lab',
            'konten'  => 'Konten berita.',
            'user_id' => $kepala->id,
        ]);
        $id = $berita->id;
        $this->actingAs($kepala);

        $response = $this->delete(route('admin.berita.destroy', $berita));

        $response->assertRedirectToRoute('admin.berita.index');
        $this->assertDatabaseMissing('beritas', ['id' => $id]);
    }
}
