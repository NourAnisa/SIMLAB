<?php

namespace Tests\Feature;

use App\Models\IzinPenelitian;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KepalaLabIzinPenelitianTest extends TestCase
{
    use RefreshDatabase;

    private function makeKepalaLab(): User
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'kepala_lab', 'profile_completed' => true]);
        return $user;
    }

    public function test_kepala_lab_can_view_izin_list(): void
    {
        $this->actingAs($this->makeKepalaLab());

        $response = $this->get(route('kepala_lab.izin-penelitian.index'));

        $response->assertStatus(200);
    }

    public function test_kepala_lab_can_show_izin_detail(): void
    {
        $kepala = $this->makeKepalaLab();
        $izin   = IzinPenelitian::factory()->create(['status' => 'diverifikasi']);
        $this->actingAs($kepala);

        $response = $this->get(route('kepala_lab.izin-penelitian.show', $izin));

        $response->assertStatus(200);
    }

    public function test_kepala_lab_can_setujui_izin(): void
    {
        $kepala = $this->makeKepalaLab();
        $izin   = IzinPenelitian::factory()->create(['status' => 'diverifikasi']);
        $this->actingAs($kepala);

        $response = $this->post(route('kepala_lab.izin-penelitian.setujui', $izin));

        $response->assertRedirect();
        $this->assertDatabaseHas('izin_penelitian', ['id' => $izin->id, 'status' => 'disetujui']);
    }

    public function test_kepala_lab_can_tolak_izin(): void
    {
        $kepala = $this->makeKepalaLab();
        $izin   = IzinPenelitian::factory()->create(['status' => 'diverifikasi']);
        $this->actingAs($kepala);

        $response = $this->post(route('kepala_lab.izin-penelitian.tolak', $izin), [
            'catatan_verifikasi' => 'Tidak memenuhi persyaratan.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('izin_penelitian', ['id' => $izin->id, 'status' => 'ditolak']);
    }

    public function test_non_kepala_lab_cannot_setujui_izin(): void
    {
        /** @var User $dosen */
        $dosen = User::factory()->create(['role' => 'dosen']);
        $izin  = IzinPenelitian::factory()->create(['status' => 'diverifikasi']);
        $this->actingAs($dosen);

        $response = $this->post(route('kepala_lab.izin-penelitian.setujui', $izin));

        $response->assertForbidden();
    }
}
