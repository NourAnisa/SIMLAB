<?php

namespace Tests\Feature;

use App\Models\PeminjamanLab;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DosenPeminjamanLabTest extends TestCase
{
    use RefreshDatabase;

    private function makeDosen(): User
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'dosen', 'profile_completed' => true]);
        return $user;
    }

    public function test_dosen_can_view_peminjaman_index(): void
    {
        $this->actingAs($this->makeDosen());

        $response = $this->get(route('dosen.peminjaman-lab.index'));

        $response->assertStatus(200);
    }

    public function test_dosen_can_view_create_form(): void
    {
        $this->actingAs($this->makeDosen());

        $response = $this->get(route('dosen.peminjaman-lab.create'));

        $response->assertStatus(200);
    }

    public function test_dosen_can_submit_peminjaman(): void
    {
        $dosen = $this->makeDosen();
        $this->actingAs($dosen);

        $response = $this->post(route('dosen.peminjaman-lab.store'), [
            'ruangan'        => 'Lab Komputer A',
            'tanggal_pinjam' => now()->addDay()->format('Y-m-d'),
            'jam_mulai'      => '08:00',
            'jam_selesai'    => '10:00',
            'keperluan'      => 'Praktikum mata kuliah algoritma',
        ]);

        $response->assertRedirect(route('dosen.peminjaman-lab.index'));
        $this->assertDatabaseHas('peminjaman_lab', [
            'user_id' => $dosen->id,
            'ruangan' => 'Lab Komputer A',
            'status'  => 'menunggu',
        ]);
    }

    public function test_dosen_can_view_own_peminjaman(): void
    {
        $dosen      = $this->makeDosen();
        $peminjaman = PeminjamanLab::factory()->create(['user_id' => $dosen->id]);
        $this->actingAs($dosen);

        $response = $this->get(route('dosen.peminjaman-lab.show', $peminjaman));

        $response->assertStatus(200);
    }

    public function test_dosen_cannot_view_others_peminjaman(): void
    {
        $dosen1     = $this->makeDosen();
        $dosen2     = $this->makeDosen();
        $peminjaman = PeminjamanLab::factory()->create(['user_id' => $dosen2->id]);
        $this->actingAs($dosen1);

        $response = $this->get(route('dosen.peminjaman-lab.show', $peminjaman));

        $response->assertForbidden();
    }

    public function test_dosen_can_kembalikan_own_peminjaman(): void
    {
        $dosen      = $this->makeDosen();
        $peminjaman = PeminjamanLab::factory()->create([
            'user_id' => $dosen->id,
            'status'  => 'disetujui',
        ]);
        $this->actingAs($dosen);

        $response = $this->patch(route('dosen.peminjaman-lab.kembalikan', $peminjaman));

        $response->assertRedirect();
        $this->assertDatabaseHas('peminjaman_lab', [
            'id'     => $peminjaman->id,
            'status' => 'menunggu_kembali',
        ]);
    }

    public function test_dosen_cannot_kembalikan_others_peminjaman(): void
    {
        $dosen1     = $this->makeDosen();
        $dosen2     = $this->makeDosen();
        $peminjaman = PeminjamanLab::factory()->create([
            'user_id' => $dosen2->id,
            'status'  => 'disetujui',
        ]);
        $this->actingAs($dosen1);

        $response = $this->patch(route('dosen.peminjaman-lab.kembalikan', $peminjaman));

        $response->assertForbidden();
    }

    public function test_mahasiswa_cannot_access_dosen_peminjaman(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'mahasiswa']);
        $this->actingAs($user);

        $response = $this->get(route('dosen.peminjaman-lab.index'));

        $response->assertForbidden();
    }
}
