<?php

namespace Tests\Unit\Controllers;

use App\Models\User;
use App\Models\SuratBebasLab;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MahasiswaSuratBebasLabControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_mahasiswa_can_view_surat_bebas_list()
    {
        $user = User::factory()->create(['role' => 'mahasiswa', 'profile_completed' => true]);
        $this->actingAs($user);

        $response = $this->get(route('mahasiswa.surat-bebas-lab.index'));

        $response->assertStatus(200);
    }

    public function test_mahasiswa_can_submit_surat_bebas_lab()
    {
        $user = User::factory()->create(['role' => 'mahasiswa', 'profile_completed' => true]);
        $this->actingAs($user);

        $response = $this->post(route('mahasiswa.surat-bebas-lab.store'));

        $response->assertRedirect(route('mahasiswa.surat-bebas-lab.index'));
        $this->assertDatabaseHas('surat_bebas_lab', [
            'user_id' => $user->id,
            'status'  => 'menunggu',
        ]);
    }

    public function test_mahasiswa_cannot_view_others_surat()
    {
        $user1 = User::factory()->create(['role' => 'mahasiswa', 'profile_completed' => true]);
        $user2 = User::factory()->create(['role' => 'mahasiswa', 'profile_completed' => true]);
        $surat = SuratBebasLab::factory()->create(['user_id' => $user2->id]);
        $this->actingAs($user1);

        $response = $this->get(route('mahasiswa.surat-bebas-lab.show', $surat));

        $response->assertForbidden();
    }

    public function test_cetak_blocked_if_status_not_disetujui()
    {
        $user  = User::factory()->create(['role' => 'mahasiswa', 'profile_completed' => true]);
        $surat = SuratBebasLab::factory()->create([
            'user_id' => $user->id,
            'status'  => 'menunggu',
        ]);
        $this->actingAs($user);

        $response = $this->get(route('mahasiswa.surat-bebas-lab.cetak', $surat));

        $response->assertForbidden();
    }

    public function test_admin_can_verifikasi_surat()
    {
        $admin = User::factory()->create(['role' => 'admin', 'profile_completed' => true]);
        $surat = SuratBebasLab::factory()->create(['status' => 'menunggu']);
        $this->actingAs($admin);

        $response = $this->post(route('admin.surat-bebas-lab.verifikasi', $surat));

        $response->assertRedirect();
        $this->assertDatabaseHas('surat_bebas_lab', [
            'id'     => $surat->id,
            'status' => 'diverifikasi',
        ]);
    }

    public function test_kepala_lab_can_setujui_surat()
    {
        $kepala = User::factory()->create(['role' => 'kepala_lab', 'profile_completed' => true]);
        $surat  = SuratBebasLab::factory()->create(['status' => 'diverifikasi']);
        $this->actingAs($kepala);

        $response = $this->post(route('kepala_lab.surat-bebas-lab.setujui', $surat));

        $response->assertRedirect();
        $this->assertDatabaseHas('surat_bebas_lab', [
            'id'     => $surat->id,
            'status' => 'disetujui',
        ]);
    }
}
