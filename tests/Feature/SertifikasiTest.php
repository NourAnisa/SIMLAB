<?php

namespace Tests\Feature;

use App\Models\JadwalSertifikasi;
use App\Models\PendaftaranSertifikasi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Feature: Sertifikasi — alur lengkap semua role.
 *
 * Status flow:
 *   menunggu → (admin) diverifikasi → (kepala_lab) lulus / tidak_lulus
 */
class SertifikasiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
        Storage::fake('public');
    }

    // ─────────────────────────────────────────────────────────────
    // MAHASISWA
    // ─────────────────────────────────────────────────────────────

    private function makeMahasiswa(bool $complete = true): User
    {
        /** @var User $user */
        $user = User::factory()->create([
            'role'              => 'mahasiswa',
            'profile_completed' => $complete,
            'nim_nip'           => $complete ? '20210001' : null,
            'jurusan'           => $complete ? 'Teknik Informatika' : null,
            'foto_profil'       => $complete ? 'foto_profil/test.jpg' : null,
        ]);
        return $user;
    }

    public function test_mahasiswa_lengkap_bisa_lihat_jadwal_sertifikasi(): void
    {
        $this->actingAs($this->makeMahasiswa());

        $this->get(route('mahasiswa.sertifikasi.index'))->assertOk();
    }

    public function test_mahasiswa_profil_belum_lengkap_diredirect_ke_profil(): void
    {
        $this->actingAs($this->makeMahasiswa(false));

        $this->get(route('mahasiswa.sertifikasi.index'))
            ->assertRedirect(route('profile.edit'));
    }

    public function test_bukan_mahasiswa_tidak_bisa_akses_halaman_sertifikasi(): void
    {
        /** @var User $dosen */
        $dosen = User::factory()->create(['role' => 'dosen']);
        $this->actingAs($dosen);

        $this->get(route('mahasiswa.sertifikasi.index'))->assertForbidden();
    }

    public function test_tamu_diredirect_ke_login(): void
    {
        $this->get(route('mahasiswa.sertifikasi.index'))
            ->assertRedirect(route('login'));
    }

    public function test_mahasiswa_berhasil_daftar_ke_jadwal(): void
    {
        $user   = $this->makeMahasiswa();
        $jadwal = JadwalSertifikasi::factory()->create(['kuota' => 10]);
        $this->actingAs($user);

        $response = $this->post(route('mahasiswa.sertifikasi.daftar.store', $jadwal), [
            'dokumen_berkas' => UploadedFile::fake()->create('berkas.pdf', 200, 'application/pdf'),
        ]);

        $response->assertRedirect(route('mahasiswa.sertifikasi.riwayat'));
        $this->assertDatabaseHas('pendaftaran_sertifikasi', [
            'user_id'   => $user->id,
            'jadwal_id' => $jadwal->id,
            'status'    => 'menunggu',
        ]);
    }

    public function test_daftar_sertifikasi_wajib_upload_dokumen(): void
    {
        $user   = $this->makeMahasiswa();
        $jadwal = JadwalSertifikasi::factory()->create(['kuota' => 5]);
        $this->actingAs($user);

        $this->post(route('mahasiswa.sertifikasi.daftar.store', $jadwal), [])
            ->assertSessionHasErrors(['dokumen_berkas']);
    }

    public function test_mahasiswa_bisa_lihat_riwayat_sertifikasi(): void
    {
        $this->actingAs($this->makeMahasiswa());

        $this->get(route('mahasiswa.sertifikasi.riwayat'))->assertOk();
    }

    public function test_cetak_diizinkan_jika_status_lulus(): void
    {
        $user        = $this->makeMahasiswa();
        $pendaftaran = PendaftaranSertifikasi::factory()->create([
            'user_id' => $user->id,
            'status'  => 'lulus',
        ]);
        $this->actingAs($user);

        $this->get(route('mahasiswa.sertifikasi.cetak', $pendaftaran))->assertOk();
    }

    public function test_cetak_diizinkan_jika_status_tidak_lulus(): void
    {
        $user        = $this->makeMahasiswa();
        $pendaftaran = PendaftaranSertifikasi::factory()->create([
            'user_id' => $user->id,
            'status'  => 'tidak_lulus',
        ]);
        $this->actingAs($user);

        $this->get(route('mahasiswa.sertifikasi.cetak', $pendaftaran))->assertOk();
    }

    public function test_cetak_diblokir_jika_status_menunggu(): void
    {
        $user        = $this->makeMahasiswa();
        $pendaftaran = PendaftaranSertifikasi::factory()->create([
            'user_id' => $user->id,
            'status'  => 'menunggu',
        ]);
        $this->actingAs($user);

        $this->get(route('mahasiswa.sertifikasi.cetak', $pendaftaran))->assertForbidden();
    }

    public function test_cetak_diblokir_jika_status_diverifikasi(): void
    {
        $user        = $this->makeMahasiswa();
        $pendaftaran = PendaftaranSertifikasi::factory()->create([
            'user_id' => $user->id,
            'status'  => 'diverifikasi',
        ]);
        $this->actingAs($user);

        $this->get(route('mahasiswa.sertifikasi.cetak', $pendaftaran))->assertForbidden();
    }

    public function test_mahasiswa_tidak_bisa_cetak_milik_orang_lain(): void
    {
        $user1       = $this->makeMahasiswa();
        $user2       = $this->makeMahasiswa();
        $pendaftaran = PendaftaranSertifikasi::factory()->create([
            'user_id' => $user2->id,
            'status'  => 'lulus',
        ]);
        $this->actingAs($user1);

        $this->get(route('mahasiswa.sertifikasi.cetak', $pendaftaran))->assertForbidden();
    }

    // ─────────────────────────────────────────────────────────────
    // ADMIN
    // ─────────────────────────────────────────────────────────────

    private function makeAdmin(): User
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'admin']);
        return $user;
    }

    public function test_admin_bisa_lihat_daftar_pendaftaran_sertifikasi(): void
    {
        $this->actingAs($this->makeAdmin());

        $this->get(route('admin.sertifikasi.index'))->assertOk();
    }

    public function test_non_admin_tidak_bisa_akses_sertifikasi_admin(): void
    {
        $mahasiswa = $this->makeMahasiswa();
        $this->actingAs($mahasiswa);

        $this->get(route('admin.sertifikasi.index'))->assertForbidden();
    }

    public function test_admin_berhasil_verifikasi_pendaftaran(): void
    {
        $admin       = $this->makeAdmin();
        $pendaftaran = PendaftaranSertifikasi::factory()->create(['status' => 'menunggu']);
        $this->actingAs($admin);

        $this->patch(route('admin.sertifikasi.verifikasi', $pendaftaran))
            ->assertRedirect();

        $this->assertDatabaseHas('pendaftaran_sertifikasi', [
            'id'     => $pendaftaran->id,
            'status' => 'diverifikasi',
        ]);
    }

    public function test_admin_diblokir_dari_route_nilai_kepala_lab(): void
    {
        $admin       = $this->makeAdmin();
        $pendaftaran = PendaftaranSertifikasi::factory()->create(['status' => 'diverifikasi']);
        $this->actingAs($admin);

        $this->patch(route('kepala_lab.sertifikasi.nilai', $pendaftaran), [
            'status' => 'lulus',
            'nilai'  => 85,
        ])->assertForbidden();
    }

    // ─────────────────────────────────────────────────────────────
    // KEPALA LAB
    // ─────────────────────────────────────────────────────────────

    private function makeKepalaLab(): User
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'kepala_lab']);
        return $user;
    }

    public function test_kepala_lab_bisa_lihat_daftar_sertifikasi(): void
    {
        $this->actingAs($this->makeKepalaLab());

        $this->get(route('kepala_lab.sertifikasi.index'))->assertOk();
    }

    public function test_kepala_lab_dapat_memberi_nilai_lulus(): void
    {
        $kepala      = $this->makeKepalaLab();
        $pendaftaran = PendaftaranSertifikasi::factory()->create(['status' => 'diverifikasi']);
        $this->actingAs($kepala);

        $this->patch(route('kepala_lab.sertifikasi.nilai', $pendaftaran), [
            'status' => 'lulus',
            'nilai'  => 85,
        ])->assertRedirect();

        $this->assertDatabaseHas('pendaftaran_sertifikasi', [
            'id'     => $pendaftaran->id,
            'status' => 'lulus',
            'nilai'  => 85,
        ]);
    }

    public function test_kepala_lab_dapat_memberi_nilai_tidak_lulus(): void
    {
        $kepala      = $this->makeKepalaLab();
        $pendaftaran = PendaftaranSertifikasi::factory()->create(['status' => 'diverifikasi']);
        $this->actingAs($kepala);

        $this->patch(route('kepala_lab.sertifikasi.nilai', $pendaftaran), [
            'status' => 'tidak_lulus',
            'nilai'  => 40,
        ])->assertRedirect();

        $this->assertDatabaseHas('pendaftaran_sertifikasi', [
            'id'     => $pendaftaran->id,
            'status' => 'tidak_lulus',
        ]);
    }

    public function test_nilai_wajib_diisi(): void
    {
        $kepala      = $this->makeKepalaLab();
        $pendaftaran = PendaftaranSertifikasi::factory()->create(['status' => 'diverifikasi']);
        $this->actingAs($kepala);

        $this->patch(route('kepala_lab.sertifikasi.nilai', $pendaftaran), [
            'status' => 'lulus',
        ])->assertSessionHasErrors(['nilai']);
    }

    public function test_status_nilai_harus_lulus_atau_tidak_lulus(): void
    {
        $kepala      = $this->makeKepalaLab();
        $pendaftaran = PendaftaranSertifikasi::factory()->create(['status' => 'diverifikasi']);
        $this->actingAs($kepala);

        $this->patch(route('kepala_lab.sertifikasi.nilai', $pendaftaran), [
            'status' => 'menunggu',
            'nilai'  => 75,
        ])->assertSessionHasErrors(['status']);
    }

    public function test_mahasiswa_tidak_bisa_akses_kepala_lab_sertifikasi(): void
    {
        $mahasiswa = $this->makeMahasiswa();
        $this->actingAs($mahasiswa);

        $this->get(route('kepala_lab.sertifikasi.index'))->assertForbidden();
    }
}
