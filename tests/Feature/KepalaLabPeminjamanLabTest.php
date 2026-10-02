<?php

namespace Tests\Feature;

use App\Models\PeminjamanLab;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KepalaLabPeminjamanLabTest extends TestCase
{
    use RefreshDatabase;

    private function makeKepalaLab(): User
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'kepala_lab', 'profile_completed' => true]);
        return $user;
    }

    public function test_kepala_lab_can_view_peminjaman_list(): void
    {
        $this->actingAs($this->makeKepalaLab());

        $response = $this->get(route('kepala_lab.peminjaman-lab.index'));

        $response->assertStatus(200);
    }

    public function test_kepala_lab_can_show_peminjaman_detail(): void
    {
        $kepala     = $this->makeKepalaLab();
        $peminjaman = PeminjamanLab::factory()->create(['status' => 'diverifikasi']);
        $this->actingAs($kepala);

        $response = $this->get(route('kepala_lab.peminjaman-lab.show', $peminjaman));

        $response->assertStatus(200);
    }

    public function test_kepala_lab_can_setujui_peminjaman(): void
    {
        $kepala     = $this->makeKepalaLab();
        $peminjaman = PeminjamanLab::factory()->create(['status' => 'diverifikasi']);
        $this->actingAs($kepala);

        $response = $this->post(route('kepala_lab.peminjaman-lab.setujui', $peminjaman));

        $response->assertRedirect();
        $this->assertDatabaseHas('peminjaman_lab', ['id' => $peminjaman->id, 'status' => 'disetujui']);
    }

    public function test_kepala_lab_can_tolak_peminjaman(): void
    {
        $kepala     = $this->makeKepalaLab();
        $peminjaman = PeminjamanLab::factory()->create(['status' => 'diverifikasi']);
        $this->actingAs($kepala);

        $response = $this->post(route('kepala_lab.peminjaman-lab.tolak', $peminjaman));

        $response->assertRedirect();
        $this->assertDatabaseHas('peminjaman_lab', ['id' => $peminjaman->id, 'status' => 'ditolak']);
    }

    public function test_mahasiswa_cannot_setujui_peminjaman_via_kepala_lab(): void
    {
        /** @var User $user */
        $user       = User::factory()->create(['role' => 'mahasiswa']);
        $peminjaman = PeminjamanLab::factory()->create(['status' => 'diverifikasi']);
        $this->actingAs($user);

        $response = $this->post(route('kepala_lab.peminjaman-lab.setujui', $peminjaman));

        $response->assertForbidden();
    }
}
