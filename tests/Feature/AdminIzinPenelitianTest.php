<?php

namespace Tests\Feature;

use App\Models\IzinPenelitian;
use App\Models\PeminjamanLab;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminIzinPenelitianTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): User
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'admin', 'profile_completed' => true]);
        return $user;
    }

    public function test_admin_can_view_izin_list(): void
    {
        $this->actingAs($this->makeAdmin());

        $response = $this->get(route('admin.izin-penelitian.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_show_izin_detail(): void
    {
        $admin = $this->makeAdmin();
        $izin  = IzinPenelitian::factory()->create(['status' => 'menunggu']);
        $this->actingAs($admin);

        $response = $this->get(route('admin.izin-penelitian.show', $izin));

        $response->assertStatus(200);
    }

    public function test_admin_can_verifikasi_izin(): void
    {
        $admin = $this->makeAdmin();
        $izin  = IzinPenelitian::factory()->create(['status' => 'menunggu']);
        $this->actingAs($admin);

        $response = $this->post(route('admin.izin-penelitian.verifikasi', $izin));

        $response->assertRedirect();
        $this->assertDatabaseHas('izin_penelitian', ['id' => $izin->id, 'status' => 'diverifikasi']);
    }

    public function test_admin_can_tolak_izin(): void
    {
        $admin = $this->makeAdmin();
        $izin  = IzinPenelitian::factory()->create(['status' => 'menunggu']);
        $this->actingAs($admin);

        $response = $this->post(route('admin.izin-penelitian.tolak', $izin), [
            'catatan_verifikasi' => 'Dokumen tidak lengkap.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('izin_penelitian', ['id' => $izin->id, 'status' => 'ditolak']);
    }

    public function test_non_admin_cannot_access_izin_management(): void
    {
        /** @var User $mahasiswa */
        $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);
        $this->actingAs($mahasiswa);

        $response = $this->get(route('admin.izin-penelitian.index'));

        $response->assertForbidden();
    }
}
