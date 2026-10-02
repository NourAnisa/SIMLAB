<?php

namespace Tests\Feature;

use App\Models\SuratBebasLab;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KepalaLabSuratBebasLabTest extends TestCase
{
    use RefreshDatabase;

    private function makeKepalaLab(): User
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'kepala_lab', 'profile_completed' => true]);
        return $user;
    }

    public function test_kepala_lab_can_view_surat_list(): void
    {
        $this->actingAs($this->makeKepalaLab());

        $response = $this->get(route('kepala_lab.surat-bebas-lab.index'));

        $response->assertStatus(200);
    }

    public function test_kepala_lab_can_show_surat_detail(): void
    {
        $kepala = $this->makeKepalaLab();
        $surat  = SuratBebasLab::factory()->create(['status' => 'diverifikasi']);
        $this->actingAs($kepala);

        $response = $this->get(route('kepala_lab.surat-bebas-lab.show', $surat));

        $response->assertStatus(200);
    }

    public function test_kepala_lab_can_setujui_surat(): void
    {
        $kepala = $this->makeKepalaLab();
        $surat  = SuratBebasLab::factory()->create(['status' => 'diverifikasi']);
        $this->actingAs($kepala);

        $response = $this->post(route('kepala_lab.surat-bebas-lab.setujui', $surat));

        $response->assertRedirect();
        $this->assertDatabaseHas('surat_bebas_lab', ['id' => $surat->id, 'status' => 'disetujui']);
    }

    public function test_kepala_lab_can_cetak_surat_when_disetujui(): void
    {
        $kepala = $this->makeKepalaLab();
        $surat  = SuratBebasLab::factory()->create(['status' => 'disetujui']);
        $this->actingAs($kepala);

        $response = $this->get(route('kepala_lab.surat-bebas-lab.cetak', $surat));

        $response->assertStatus(200);
    }

    public function test_non_kepala_lab_cannot_setujui_surat(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin']);
        $surat = SuratBebasLab::factory()->create(['status' => 'diverifikasi']);
        $this->actingAs($admin);

        $response = $this->post(route('kepala_lab.surat-bebas-lab.setujui', $surat));

        $response->assertForbidden();
    }
}
