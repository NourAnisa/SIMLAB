<?php

namespace Tests\Feature;

use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRuanganTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): User
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'admin', 'profile_completed' => true]);
        return $user;
    }

    public function test_admin_can_view_ruangan_index(): void
    {
        $this->actingAs($this->makeAdmin());

        $response = $this->get(route('admin.ruangan.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_create_ruangan(): void
    {
        $this->actingAs($this->makeAdmin());

        $response = $this->post(route('admin.ruangan.store'), [
            'nama_ruangan' => 'Lab Jaringan',
            'fasilitas'    => 'Router, Switch, PC',
            'deskripsi'    => 'Laboratorium jaringan komputer',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('ruangans', ['nama_ruangan' => 'Lab Jaringan']);
    }

    public function test_admin_cannot_create_duplicate_ruangan(): void
    {
        $this->actingAs($this->makeAdmin());
        Ruangan::create(['nama_ruangan' => 'Lab A']);

        $response = $this->post(route('admin.ruangan.store'), [
            'nama_ruangan' => 'Lab A',
        ]);

        $response->assertSessionHasErrors(['nama_ruangan']);
    }

    public function test_admin_can_update_ruangan(): void
    {
        $admin   = $this->makeAdmin();
        $ruangan = Ruangan::create(['nama_ruangan' => 'Lab Lama']);
        $this->actingAs($admin);

        $response = $this->put(route('admin.ruangan.update', $ruangan), [
            'nama_ruangan' => 'Lab Baru',
            'fasilitas'    => 'Projector',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('ruangans', ['id' => $ruangan->id, 'nama_ruangan' => 'Lab Baru']);
    }

    public function test_admin_can_delete_ruangan(): void
    {
        $admin   = $this->makeAdmin();
        $ruangan = Ruangan::create(['nama_ruangan' => 'Lab Hapus']);
        $this->actingAs($admin);

        $response = $this->delete(route('admin.ruangan.destroy', $ruangan));

        $response->assertRedirect();
        $this->assertDatabaseMissing('ruangans', ['id' => $ruangan->id]);
    }

    public function test_mahasiswa_cannot_manage_ruangan(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'mahasiswa']);
        $this->actingAs($user);

        $response = $this->get(route('admin.ruangan.index'));

        $response->assertForbidden();
    }
}
