<?php

namespace Tests\Feature;

use App\Models\JadwalSertifikasi;
use App\Models\SkemaSertifikasi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminJadwalTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): User
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'admin', 'profile_completed' => true]);
        return $user;
    }

    public function test_admin_can_view_jadwal_index(): void
    {
        $this->actingAs($this->makeAdmin());

        $response = $this->get(route('admin.jadwal.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_create_jadwal(): void
    {
        $admin = $this->makeAdmin();
        $skema = SkemaSertifikasi::factory()->create();
        $this->actingAs($admin);

        $response = $this->post(route('admin.jadwal.store'), [
            'skema_id' => $skema->id,
            'tanggal'  => now()->addDays(10)->format('Y-m-d'),
            'kuota'    => 30,
        ]);

        $response->assertRedirect(route('admin.jadwal.index'));
        $this->assertDatabaseHas('jadwal_sertifikasi', ['skema_id' => $skema->id, 'kuota' => 30]);
    }

    public function test_admin_store_jadwal_requires_fields(): void
    {
        $this->actingAs($this->makeAdmin());

        $response = $this->post(route('admin.jadwal.store'), []);

        $response->assertSessionHasErrors(['skema_id', 'tanggal', 'kuota']);
    }

    public function test_admin_can_update_jadwal(): void
    {
        $admin  = $this->makeAdmin();
        $jadwal = JadwalSertifikasi::factory()->create(['kuota' => 10]);
        $this->actingAs($admin);

        $response = $this->put(route('admin.jadwal.update', $jadwal), [
            'skema_id' => $jadwal->skema_id,
            'tanggal'  => now()->addDays(15)->format('Y-m-d'),
            'kuota'    => 50,
        ]);

        $response->assertRedirect(route('admin.jadwal.index'));
        $this->assertDatabaseHas('jadwal_sertifikasi', ['id' => $jadwal->id, 'kuota' => 50]);
    }

    public function test_admin_can_delete_jadwal(): void
    {
        $admin  = $this->makeAdmin();
        $jadwal = JadwalSertifikasi::factory()->create();
        $this->actingAs($admin);

        $response = $this->delete(route('admin.jadwal.destroy', $jadwal));

        $response->assertRedirect(route('admin.jadwal.index'));
        $this->assertDatabaseMissing('jadwal_sertifikasi', ['id' => $jadwal->id]);
    }

    public function test_non_admin_cannot_manage_jadwal(): void
    {
        /** @var User $kepala */
        $kepala = User::factory()->create(['role' => 'kepala_lab']);
        $this->actingAs($kepala);

        $response = $this->get(route('admin.jadwal.index'));

        $response->assertForbidden();
    }
}
