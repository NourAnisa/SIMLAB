<?php

namespace Tests\Feature;

use App\Models\SkemaSertifikasi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminSkemaTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): User
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'admin', 'profile_completed' => true]);
        return $user;
    }

    public function test_admin_can_view_skema_index(): void
    {
        $this->actingAs($this->makeAdmin());

        $response = $this->get(route('admin.skema.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_create_skema(): void
    {
        Storage::fake('public');
        $this->actingAs($this->makeAdmin());

        $response = $this->post(route('admin.skema.store'), [
            'nama_skema' => 'Skema Junior Network Administrator',
            'deskripsi'  => 'Skema sertifikasi jaringan untuk pemula.',
        ]);

        $response->assertRedirect(route('admin.skema.index'));
        $this->assertDatabaseHas('skema_sertifikasi', ['nama_skema' => 'Skema Junior Network Administrator']);
    }

    public function test_admin_store_skema_requires_fields(): void
    {
        $this->actingAs($this->makeAdmin());

        $response = $this->post(route('admin.skema.store'), []);

        $response->assertSessionHasErrors(['nama_skema', 'deskripsi']);
    }

    public function test_admin_can_update_skema(): void
    {
        Storage::fake('public');
        $admin = $this->makeAdmin();
        $skema = SkemaSertifikasi::factory()->create();
        $this->actingAs($admin);

        $response = $this->put(route('admin.skema.update', $skema), [
            'nama_skema' => 'Skema Diperbarui',
            'deskripsi'  => 'Deskripsi baru yang sudah diperbarui.',
        ]);

        $response->assertRedirect(route('admin.skema.index'));
        $this->assertDatabaseHas('skema_sertifikasi', ['id' => $skema->id, 'nama_skema' => 'Skema Diperbarui']);
    }

    public function test_admin_can_delete_skema(): void
    {
        Storage::fake('public');
        $admin = $this->makeAdmin();
        $skema = SkemaSertifikasi::factory()->create();
        $this->actingAs($admin);

        $response = $this->delete(route('admin.skema.destroy', $skema));

        $response->assertRedirect(route('admin.skema.index'));
        $this->assertDatabaseMissing('skema_sertifikasi', ['id' => $skema->id]);
    }

    public function test_non_admin_cannot_manage_skema(): void
    {
        /** @var User $mahasiswa */
        $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);
        $this->actingAs($mahasiswa);

        $response = $this->get(route('admin.skema.index'));

        $response->assertForbidden();
    }
}
