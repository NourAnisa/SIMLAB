<?php

namespace Tests\Feature;

use App\Models\PeminjamanLab;
use App\Models\SuratBebasLab;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

/**
 * Feature: Surat Bebas Lab — alur lengkap semua role.
 *
 * Status flow:
 *   menunggu → (admin) diverifikasi / masih_tanggungan
 *            → (kepala_lab) disetujui
 *
 * Syarat submit: tidak boleh ada peminjaman aktif (tanggungan).
 */
class SuratBebasLabTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
    }

    // ─────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────

    private function makeMahasiswa(bool $complete = true): User
    {
        return User::factory()->create([
            'role'              => 'mahasiswa',
            'profile_completed' => $complete,
            'nim_nip'           => $complete ? '20210001' : null,
            'jurusan'           => $complete ? 'Teknik Informatika' : null,
            'foto_profil'       => $complete ? 'foto_profil/test.jpg' : null,
        ]);
    }

    private function makeAdmin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function makeKepalaLab(): User
    {
        return User::factory()->create(['role' => 'kepala_lab']);
    }

    // ─────────────────────────────────────────────────────────────
    // MAHASISWA
    // ─────────────────────────────────────────────────────────────

    public function test_mahasiswa_bisa_akses_halaman_surat_bebas_lab(): void
    {
        $this->actingAs($this->makeMahasiswa());

        $this->get(route('mahasiswa.surat-bebas-lab.index'))->assertOk();
    }

    public function test_mahasiswa_profil_belum_lengkap_diredirect(): void
    {
        $this->actingAs($this->makeMahasiswa(false));

        $this->get(route('mahasiswa.surat-bebas-lab.index'))
            ->assertRedirect(route('profile.edit'));
    }

    public function test_mahasiswa_tanpa_tanggungan_berhasil_submit(): void
    {
        $user = $this->makeMahasiswa();
        $this->actingAs($user);

        $this->post(route('mahasiswa.surat-bebas-lab.store'))->assertRedirect();

        $this->assertDatabaseHas('surat_bebas_lab', [
            'user_id' => $user->id,
            'status'  => 'menunggu',
        ]);
    }

    public function test_submit_gagal_jika_ada_peminjaman_aktif(): void
    {
        $user = $this->makeMahasiswa();
        PeminjamanLab::factory()->create([
            'user_id' => $user->id,
            'status'  => 'disetujui',
        ]);
        $this->actingAs($user);

        $this->post(route('mahasiswa.surat-bebas-lab.store'))->assertSessionHasErrors();

        $this->assertDatabaseMissing('surat_bebas_lab', ['user_id' => $user->id]);
    }

    public function test_mahasiswa_bisa_lihat_surat_milik_sendiri(): void
    {
        $user  = $this->makeMahasiswa();
        $surat = SuratBebasLab::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $this->get(route('mahasiswa.surat-bebas-lab.show', $surat))->assertOk();
    }

    public function test_mahasiswa_tidak_bisa_lihat_surat_orang_lain(): void
    {
        $user1 = $this->makeMahasiswa();
        $user2 = $this->makeMahasiswa();
        $surat = SuratBebasLab::factory()->create(['user_id' => $user2->id]);
        $this->actingAs($user1);

        $this->get(route('mahasiswa.surat-bebas-lab.show', $surat))->assertForbidden();
    }

    public function test_cetak_diizinkan_jika_status_disetujui(): void
    {
        $user  = $this->makeMahasiswa();
        $surat = SuratBebasLab::factory()->create(['user_id' => $user->id, 'status' => 'disetujui']);
        $this->actingAs($user);

        $this->get(route('mahasiswa.surat-bebas-lab.cetak', $surat))->assertOk();
    }

    public function test_cetak_diblokir_jika_masih_menunggu(): void
    {
        $user  = $this->makeMahasiswa();
        $surat = SuratBebasLab::factory()->create(['user_id' => $user->id, 'status' => 'menunggu']);
        $this->actingAs($user);

        $this->get(route('mahasiswa.surat-bebas-lab.cetak', $surat))->assertForbidden();
    }

    public function test_mahasiswa_tidak_bisa_cetak_surat_orang_lain(): void
    {
        $user1 = $this->makeMahasiswa();
        $user2 = $this->makeMahasiswa();
        $surat = SuratBebasLab::factory()->create(['user_id' => $user2->id, 'status' => 'disetujui']);
        $this->actingAs($user1);

        $this->get(route('mahasiswa.surat-bebas-lab.cetak', $surat))->assertForbidden();
    }

    // ─────────────────────────────────────────────────────────────
    // ADMIN
    // ─────────────────────────────────────────────────────────────

    public function test_admin_bisa_lihat_daftar_surat_bebas_lab(): void
    {
        $this->actingAs($this->makeAdmin());

        $this->get(route('admin.surat-bebas-lab.index'))->assertOk();
    }

    public function test_non_admin_diblokir_dari_halaman_admin_surat(): void
    {
        $this->actingAs($this->makeMahasiswa());

        $this->get(route('admin.surat-bebas-lab.index'))->assertForbidden();
    }

    public function test_admin_berhasil_verifikasi_surat(): void
    {
        $admin = $this->makeAdmin();
        $surat = SuratBebasLab::factory()->create(['status' => 'menunggu']);
        $this->actingAs($admin);

        $this->post(route('admin.surat-bebas-lab.verifikasi', $surat))->assertRedirect();

        $this->assertDatabaseHas('surat_bebas_lab', [
            'id'     => $surat->id,
            'status' => 'diverifikasi',
        ]);
    }

    public function test_admin_berhasil_tandai_sebagai_masih_tanggungan(): void
    {
        $admin = $this->makeAdmin();
        $surat = SuratBebasLab::factory()->create(['status' => 'menunggu']);
        $this->actingAs($admin);

        $this->post(route('admin.surat-bebas-lab.tanggungan', $surat))->assertRedirect();

        $this->assertDatabaseHas('surat_bebas_lab', [
            'id'     => $surat->id,
            'status' => 'masih_tanggungan',
        ]);
    }

    public function test_admin_bisa_lihat_detail_surat(): void
    {
        $surat = SuratBebasLab::factory()->create();
        $this->actingAs($this->makeAdmin());

        $this->get(route('admin.surat-bebas-lab.show', $surat))->assertOk();
    }

    // ─────────────────────────────────────────────────────────────
    // KEPALA LAB
    // ─────────────────────────────────────────────────────────────

    public function test_kepala_lab_bisa_lihat_daftar_surat_bebas_lab(): void
    {
        $this->actingAs($this->makeKepalaLab());

        $this->get(route('kepala_lab.surat-bebas-lab.index'))->assertOk();
    }

    public function test_mahasiswa_diblokir_dari_route_kepala_lab_surat(): void
    {
        $this->actingAs($this->makeMahasiswa());

        $this->get(route('kepala_lab.surat-bebas-lab.index'))->assertForbidden();
    }

    public function test_kepala_lab_berhasil_setujui_surat(): void
    {
        $kepala = $this->makeKepalaLab();
        $surat  = SuratBebasLab::factory()->create(['status' => 'diverifikasi']);
        $this->actingAs($kepala);

        $this->post(route('kepala_lab.surat-bebas-lab.setujui', $surat))->assertRedirect();

        $this->assertDatabaseHas('surat_bebas_lab', [
            'id'     => $surat->id,
            'status' => 'disetujui',
        ]);
    }

    public function test_kepala_lab_tidak_bisa_setujui_surat_masih_menunggu(): void
    {
        $kepala = $this->makeKepalaLab();
        $surat  = SuratBebasLab::factory()->create(['status' => 'menunggu']);
        $this->actingAs($kepala);

        $this->post(route('kepala_lab.surat-bebas-lab.setujui', $surat))->assertRedirect();

        $this->assertDatabaseHas('surat_bebas_lab', [
            'id'     => $surat->id,
            'status' => 'menunggu',
        ]);
    }

    public function test_kepala_lab_bisa_cetak_surat_yang_disetujui(): void
    {
        $kepala = $this->makeKepalaLab();
        $surat  = SuratBebasLab::factory()->create(['status' => 'disetujui']);
        $this->actingAs($kepala);

        $this->get(route('kepala_lab.surat-bebas-lab.cetak', $surat))->assertOk();
    }

    public function test_cetak_diblokir_jika_surat_belum_disetujui(): void
    {
        $kepala = $this->makeKepalaLab();
        $surat  = SuratBebasLab::factory()->create(['status' => 'diverifikasi']);
        $this->actingAs($kepala);

        $this->get(route('kepala_lab.surat-bebas-lab.cetak', $surat))->assertForbidden();
    }
}
