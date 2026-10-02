<?php

namespace Tests\Feature;

use App\Models\PendaftaranSertifikasi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KepalaLabSertifikasiTest extends TestCase
{
    use RefreshDatabase;

    private function makeKepalaLab(): User
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'kepala_lab', 'profile_completed' => true]);
        return $user;
    }

    public function test_kepala_lab_can_view_sertifikasi_index(): void
    {
        $this->actingAs($this->makeKepalaLab());

        $response = $this->get(route('kepala_lab.sertifikasi.index'));

        $response->assertStatus(200);
    }

    public function test_kepala_lab_can_show_pendaftaran_detail(): void
    {
        $kepala      = $this->makeKepalaLab();
        $pendaftaran = PendaftaranSertifikasi::factory()->create(['status' => 'diverifikasi']);
        $this->actingAs($kepala);

        $response = $this->get(route('kepala_lab.sertifikasi.show', $pendaftaran));

        $response->assertStatus(200);
    }

    public function test_kepala_lab_can_nilai_lulus(): void
    {
        $kepala      = $this->makeKepalaLab();
        $pendaftaran = PendaftaranSertifikasi::factory()->create(['status' => 'diverifikasi']);
        $this->actingAs($kepala);

        $response = $this->post(route('kepala_lab.sertifikasi.nilai', $pendaftaran), [
            'status'         => 'lulus',
            'nilai'          => 85,
            'hasil_validasi' => 'Kompeten pada semua unit.',
        ]);

        $response->assertRedirect(route('kepala_lab.sertifikasi.index'));
        $this->assertDatabaseHas('pendaftaran_sertifikasi', [
            'id'     => $pendaftaran->id,
            'status' => 'lulus',
            'nilai'  => 85,
        ]);
    }

    public function test_kepala_lab_can_nilai_tidak_lulus(): void
    {
        $kepala      = $this->makeKepalaLab();
        $pendaftaran = PendaftaranSertifikasi::factory()->create(['status' => 'diverifikasi']);
        $this->actingAs($kepala);

        $response = $this->post(route('kepala_lab.sertifikasi.nilai', $pendaftaran), [
            'status' => 'tidak_lulus',
            'nilai'  => 45,
        ]);

        $response->assertRedirect(route('kepala_lab.sertifikasi.index'));
        $this->assertDatabaseHas('pendaftaran_sertifikasi', [
            'id'     => $pendaftaran->id,
            'status' => 'tidak_lulus',
        ]);
    }

    public function test_nilai_requires_status_and_nilai(): void
    {
        $kepala      = $this->makeKepalaLab();
        $pendaftaran = PendaftaranSertifikasi::factory()->create(['status' => 'diverifikasi']);
        $this->actingAs($kepala);

        $response = $this->post(route('kepala_lab.sertifikasi.nilai', $pendaftaran), []);

        $response->assertSessionHasErrors(['status', 'nilai']);
    }

    public function test_non_kepala_lab_cannot_nilai_sertifikasi(): void
    {
        /** @var User $admin */
        $admin       = User::factory()->create(['role' => 'admin']);
        $pendaftaran = PendaftaranSertifikasi::factory()->create(['status' => 'diverifikasi']);
        $this->actingAs($admin);

        $response = $this->post(route('kepala_lab.sertifikasi.nilai', $pendaftaran), [
            'status' => 'lulus',
            'nilai'  => 80,
        ]);

        $response->assertForbidden();
    }
}
