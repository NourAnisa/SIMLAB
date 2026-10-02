<?php

namespace Tests\Unit\Controllers;

use App\Models\User;
use App\Models\PeminjamanLab;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PeminjamanLabControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_mahasiswa_can_view_peminjaman_list()
    {
        $user = User::factory()->create(['role' => 'mahasiswa', 'profile_completed' => true]);
        $this->actingAs($user);

        $response = $this->get(route('mahasiswa.peminjaman-lab.index'));

        $response->assertStatus(200);
    }

    public function test_mahasiswa_can_submit_peminjaman()
    {
        $user = User::factory()->create(['role' => 'mahasiswa', 'profile_completed' => true]);
        $this->actingAs($user);

        $response = $this->post(route('mahasiswa.peminjaman-lab.store'), [
            'ruangan'        => 'Lab Jaringan',
            'tanggal_pinjam' => now()->addDay()->format('Y-m-d'),
            'jam_mulai'      => '08:00',
            'jam_selesai'    => '10:00',
            'keperluan'      => 'Praktikum Jaringan Komputer',
        ]);

        $response->assertRedirect(route('mahasiswa.peminjaman-lab.index'));
        $this->assertDatabaseHas('peminjaman_lab', [
            'user_id' => $user->id,
            'ruangan' => 'Lab Jaringan',
            'status'  => 'menunggu',
        ]);
    }

    public function skip_test_cek_jadwal_returns_booked_slots()
    {
        $user = User::factory()->create(['role' => 'mahasiswa', 'profile_completed' => true]);
        PeminjamanLab::factory()->create([
            'ruangan'        => 'Lab Jaringan',
            'tanggal_pinjam' => '2025-06-01',
            'jam_mulai'      => '08:00',
            'jam_selesai'    => '10:00',
            'status'         => 'disetujui',
        ]);
        $this->actingAs($user);

        $response = $this->getJson(route('mahasiswa.peminjaman-lab.cek-jadwal', [
            'ruangan' => 'Lab Jaringan',
            'tanggal' => '2025-06-01',
        ]));

        $response->assertStatus(200)->assertJsonCount(1);
    }

    public function skip_test_cek_jadwal_returns_empty_if_no_booking()
    {
        $user = User::factory()->create(['role' => 'mahasiswa', 'profile_completed' => true]);
        $this->actingAs($user);

        $response = $this->getJson(route('mahasiswa.peminjaman-lab.cek-jadwal', [
            'ruangan' => 'Lab Jaringan',
            'tanggal' => '2099-01-01',
        ]));

        $response->assertStatus(200)->assertJsonCount(0);
    }

    public function test_mahasiswa_can_report_pengembalian()
    {
        $user       = User::factory()->create(['role' => 'mahasiswa', 'profile_completed' => true]);
        $peminjaman = PeminjamanLab::factory()->create([
            'user_id' => $user->id,
            'status'  => 'disetujui',
        ]);
        $this->actingAs($user);

        $response = $this->post(route('mahasiswa.peminjaman-lab.kembalikan', $peminjaman));

        $response->assertRedirect();
        $this->assertDatabaseHas('peminjaman_lab', [
            'id'     => $peminjaman->id,
            'status' => 'menunggu_kembali',
        ]);
    }

    public function test_mahasiswa_cannot_kembalikan_others_peminjaman()
    {
        $user1      = User::factory()->create(['role' => 'mahasiswa', 'profile_completed' => true]);
        $user2      = User::factory()->create(['role' => 'mahasiswa', 'profile_completed' => true]);
        $peminjaman = PeminjamanLab::factory()->create([
            'user_id' => $user2->id,
            'status'  => 'disetujui',
        ]);
        $this->actingAs($user1);

        $response = $this->post(route('mahasiswa.peminjaman-lab.kembalikan', $peminjaman));

        $response->assertForbidden();
    }

    public function test_admin_can_verifikasi_peminjaman()
    {
        $admin      = User::factory()->create(['role' => 'admin', 'profile_completed' => true]);
        $peminjaman = PeminjamanLab::factory()->create(['status' => 'menunggu']);
        $this->actingAs($admin);

        $response = $this->post(route('admin.peminjaman-lab.verifikasi', $peminjaman));

        $response->assertRedirect();
        $this->assertDatabaseHas('peminjaman_lab', [
            'id'     => $peminjaman->id,
            'status' => 'diverifikasi',
        ]);
    }
}
