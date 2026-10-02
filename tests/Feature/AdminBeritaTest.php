<?php

namespace Tests\Feature;

use App\Models\Berita;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminBeritaTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): User
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'admin', 'profile_completed' => true]);
        return $user;
    }

    private function makeKepalaLab(): User
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'kepala_lab', 'profile_completed' => true]);
        return $user;
    }

    public function test_admin_can_view_berita_index(): void
    {
        $this->actingAs($this->makeAdmin());

        $response = $this->get(route('admin.berita.index'));

        $response->assertStatus(200);
    }

    public function test_kepala_lab_can_view_berita_index(): void
    {
        $this->actingAs($this->makeKepalaLab());

        $response = $this->get(route('admin.berita.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_store_berita(): void
    {
        Storage::fake('public');
        $admin = $this->makeAdmin();
        $this->actingAs($admin);

        $response = $this->post(route('admin.berita.store'), [
            'judul'   => 'Berita Uji Coba',
            'konten'  => 'Isi berita uji coba yang cukup panjang.',
            'kategori' => 'Umum',
        ]);

        $response->assertRedirect(route('admin.berita.index'));
        $this->assertDatabaseHas('beritas', [
            'judul'   => 'Berita Uji Coba',
            'user_id' => $admin->id,
        ]);
    }

    public function test_admin_store_berita_requires_judul_and_konten(): void
    {
        $this->actingAs($this->makeAdmin());

        $response = $this->post(route('admin.berita.store'), []);

        $response->assertSessionHasErrors(['judul', 'konten']);
    }

    public function test_admin_can_update_berita(): void
    {
        Storage::fake('public');
        $admin  = $this->makeAdmin();
        $berita = Berita::create([
            'judul'   => 'Judul Lama',
            'konten'  => 'Konten lama',
            'slug'    => 'judul-lama-' . time(),
            'user_id' => $admin->id,
        ]);
        $this->actingAs($admin);

        $response = $this->put(route('admin.berita.update', $berita), [
            'judul'  => 'Judul Baru',
            'konten' => 'Konten baru yang sudah diperbarui.',
        ]);

        $response->assertRedirect(route('admin.berita.index'));
        $this->assertDatabaseHas('beritas', ['id' => $berita->id, 'judul' => 'Judul Baru']);
    }

    public function test_admin_can_delete_berita(): void
    {
        Storage::fake('public');
        $admin  = $this->makeAdmin();
        $berita = Berita::create([
            'judul'   => 'Berita Hapus',
            'konten'  => 'Konten hapus',
            'slug'    => 'berita-hapus-' . time(),
            'user_id' => $admin->id,
        ]);
        $this->actingAs($admin);

        $response = $this->delete(route('admin.berita.destroy', $berita));

        $response->assertRedirect(route('admin.berita.index'));
        $this->assertDatabaseMissing('beritas', ['id' => $berita->id]);
    }

    public function test_mahasiswa_cannot_access_berita_management(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'mahasiswa', 'profile_completed' => true]);
        $this->actingAs($user);

        $response = $this->get(route('admin.berita.index'));

        $response->assertForbidden();
    }
}
