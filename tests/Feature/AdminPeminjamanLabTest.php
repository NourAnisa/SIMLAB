<?php

namespace Tests\Feature;

use App\Models\PeminjamanLab;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPeminjamanLabTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): User
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'admin', 'profile_completed' => true]);
        return $user;
    }

    public function test_admin_can_view_peminjaman_list(): void
    {
        $this->actingAs($this->makeAdmin());

        $response = $this->get(route('admin.peminjaman-lab.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_show_peminjaman_detail(): void
    {
        $admin      = $this->makeAdmin();
        $peminjaman = PeminjamanLab::factory()->create(['status' => 'menunggu']);
        $this->actingAs($admin);

        $response = $this->get(route('admin.peminjaman-lab.show', $peminjaman));

        $response->assertStatus(200);
    }

    public function test_admin_can_verifikasi_peminjaman(): void
    {
        $admin      = $this->makeAdmin();
        $peminjaman = PeminjamanLab::factory()->create(['status' => 'menunggu']);
        $this->actingAs($admin);

        $response = $this->post(route('admin.peminjaman-lab.verifikasi', $peminjaman));

        $response->assertRedirect();
        $this->assertDatabaseHas('peminjaman_lab', ['id' => $peminjaman->id, 'status' => 'diverifikasi']);
    }

    public function test_admin_can_tolak_peminjaman(): void
    {
        $admin      = $this->makeAdmin();
        $peminjaman = PeminjamanLab::factory()->create(['status' => 'menunggu']);
        $this->actingAs($admin);

        $response = $this->post(route('admin.peminjaman-lab.tolak', $peminjaman));

        $response->assertRedirect();
        $this->assertDatabaseHas('peminjaman_lab', ['id' => $peminjaman->id, 'status' => 'ditolak']);
    }

    public function test_admin_can_verifikasi_kembali_peminjaman(): void
    {
        $admin      = $this->makeAdmin();
        $peminjaman = PeminjamanLab::factory()->create(['status' => 'menunggu_kembali']);
        $this->actingAs($admin);

        $response = $this->post(route('admin.peminjaman-lab.verifikasi-kembali', $peminjaman));

        $response->assertRedirect();
        $this->assertDatabaseHas('peminjaman_lab', ['id' => $peminjaman->id, 'status' => 'dikembalikan']);
    }

    public function test_non_admin_cannot_access_peminjaman_management(): void
    {
        /** @var User $dosen */
        $dosen = User::factory()->create(['role' => 'dosen']);
        $this->actingAs($dosen);

        $response = $this->get(route('admin.peminjaman-lab.index'));

        $response->assertForbidden();
    }
}
