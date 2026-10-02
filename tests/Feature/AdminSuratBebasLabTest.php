<?php

namespace Tests\Feature;

use App\Models\SuratBebasLab;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSuratBebasLabTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): User
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'admin', 'profile_completed' => true]);
        return $user;
    }

    public function test_admin_can_view_surat_bebas_lab_list(): void
    {
        $this->actingAs($this->makeAdmin());

        $response = $this->get(route('admin.surat-bebas-lab.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_show_surat_detail(): void
    {
        $admin = $this->makeAdmin();
        $surat = SuratBebasLab::factory()->create(['status' => 'menunggu']);
        $this->actingAs($admin);

        $response = $this->get(route('admin.surat-bebas-lab.show', $surat));

        $response->assertStatus(200);
    }

    public function test_admin_can_verifikasi_surat(): void
    {
        $admin = $this->makeAdmin();
        $surat = SuratBebasLab::factory()->create(['status' => 'menunggu']);
        $this->actingAs($admin);

        $response = $this->post(route('admin.surat-bebas-lab.verifikasi', $surat));

        $response->assertRedirect();
        $this->assertDatabaseHas('surat_bebas_lab', ['id' => $surat->id, 'status' => 'diverifikasi']);
    }

    public function test_admin_can_tandai_tanggungan(): void
    {
        $admin = $this->makeAdmin();
        $surat = SuratBebasLab::factory()->create(['status' => 'menunggu']);
        $this->actingAs($admin);

        $response = $this->post(route('admin.surat-bebas-lab.tanggungan', $surat));

        $response->assertRedirect();
        $this->assertDatabaseHas('surat_bebas_lab', ['id' => $surat->id, 'status' => 'masih_tanggungan']);
    }

    public function test_non_admin_cannot_access_surat_management(): void
    {
        /** @var User $mahasiswa */
        $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);
        $this->actingAs($mahasiswa);

        $response = $this->get(route('admin.surat-bebas-lab.index'));

        $response->assertForbidden();
    }
}
