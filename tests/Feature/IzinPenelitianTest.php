<?php

namespace Tests\Feature;

use App\Models\IzinPenelitian;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Feature: Izin Penelitian — alur lengkap semua role.
 *
 * Status flow:
 *   menunggu → (admin) diverifikasi / ditolak → (kepala_lab) disetujui / ditolak
 *
 * Catatan: Tolak (admin & kepala_lab) wajib menyertakan catatan_verifikasi.
 */
class IzinPenelitianTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
        Storage::fake('public');
    }

    // ─────────────────────────────────────────────────────────────
    // Helpers
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

    private function makeAdmin(): User
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'admin']);
        return $user;
    }

    private function makeKepalaLab(): User
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'kepala_lab']);
        return $user;
    }

    // ─────────────────────────────────────────────────────────────
    // MAHASISWA
    // ─────────────────────────────────────────────────────────────

    public function test_mahasiswa_bisa_akses_halaman_izin_penelitian(): void
    {
        $this->actingAs($this->makeMahasiswa());

        $this->get(route('mahasiswa.izin-penelitian.index'))->assertOk();
    }

    public function test_mahasiswa_profil_belum_lengkap_diredirect(): void
    {
        $this->actingAs($this->makeMahasiswa(false));

        $this->get(route('mahasiswa.izin-penelitian.index'))
            ->assertRedirect(route('profile.edit'));
    }

    public function test_mahasiswa_berhasil_ajukan_izin_penelitian(): void
    {
        $user = $this->makeMahasiswa();
        $this->actingAs($user);

        $response = $this->post(route('mahasiswa.izin-penelitian.store'), [
            'judul_penelitian' => 'Penelitian Sistem Informasi',
            'tujuan'           => 'Untuk keperluan penelitian tugas akhir.',
            'dokumen_berkas'   => UploadedFile::fake()->create('proposal.pdf', 200, 'application/pdf'),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('izin_penelitian', [
            'user_id' => $user->id,
            'status'  => 'menunggu',
        ]);
    }

    public function test_ajukan_izin_gagal_tanpa_judul(): void
    {
        $this->actingAs($this->makeMahasiswa());

        $this->post(route('mahasiswa.izin-penelitian.store'), [
            'tanggal_mulai'   => now()->addDay()->toDateString(),
            'tanggal_selesai' => now()->addDays(7)->toDateString(),
            'deskripsi'       => 'Deskripsi.',
        ])->assertSessionHasErrors(['judul_penelitian']);
    }

    public function test_ajukan_izin_gagal_tanpa_tujuan(): void
    {
        $this->actingAs($this->makeMahasiswa());

        $this->post(route('mahasiswa.izin-penelitian.store'), [
            'judul_penelitian' => 'Judul',
        ])->assertSessionHasErrors(['tujuan']);
    }

    public function test_mahasiswa_bisa_lihat_detail_izin_sendiri(): void
    {
        $user = $this->makeMahasiswa();
        $izin = IzinPenelitian::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $this->get(route('mahasiswa.izin-penelitian.show', $izin))->assertOk();
    }

    public function test_mahasiswa_tidak_bisa_lihat_izin_orang_lain(): void
    {
        $user1 = $this->makeMahasiswa();
        $user2 = $this->makeMahasiswa();
        $izin  = IzinPenelitian::factory()->create(['user_id' => $user2->id]);
        $this->actingAs($user1);

        $this->get(route('mahasiswa.izin-penelitian.show', $izin))->assertForbidden();
    }

    public function test_cetak_diizinkan_jika_status_disetujui(): void
    {
        $user = $this->makeMahasiswa();
        $izin = IzinPenelitian::factory()->create(['user_id' => $user->id, 'status' => 'disetujui']);
        $this->actingAs($user);

        $this->get(route('mahasiswa.izin-penelitian.cetak', $izin))->assertOk();
    }

    public function test_cetak_diblokir_jika_masih_menunggu(): void
    {
        $user = $this->makeMahasiswa();
        $izin = IzinPenelitian::factory()->create(['user_id' => $user->id, 'status' => 'menunggu']);
        $this->actingAs($user);

        $this->get(route('mahasiswa.izin-penelitian.cetak', $izin))->assertForbidden();
    }

    public function test_cetak_diblokir_jika_ditolak(): void
    {
        $user = $this->makeMahasiswa();
        $izin = IzinPenelitian::factory()->create(['user_id' => $user->id, 'status' => 'ditolak']);
        $this->actingAs($user);

        $this->get(route('mahasiswa.izin-penelitian.cetak', $izin))->assertForbidden();
    }

    public function test_mahasiswa_tidak_bisa_cetak_izin_orang_lain(): void
    {
        $user1 = $this->makeMahasiswa();
        $user2 = $this->makeMahasiswa();
        $izin  = IzinPenelitian::factory()->create(['user_id' => $user2->id, 'status' => 'disetujui']);
        $this->actingAs($user1);

        $this->get(route('mahasiswa.izin-penelitian.cetak', $izin))->assertForbidden();
    }

    // ─────────────────────────────────────────────────────────────
    // ADMIN
    // ─────────────────────────────────────────────────────────────

    public function test_admin_bisa_lihat_daftar_izin_penelitian(): void
    {
        $this->actingAs($this->makeAdmin());

        $this->get(route('admin.izin-penelitian.index'))->assertOk();
    }

    public function test_non_admin_diblokir_dari_halaman_admin(): void
    {
        /** @var User $dosen */
        $dosen = User::factory()->create(['role' => 'dosen']);
        $this->actingAs($dosen);

        $this->get(route('admin.izin-penelitian.index'))->assertForbidden();
    }

    public function test_admin_berhasil_verifikasi_izin(): void
    {
        $admin = $this->makeAdmin();
        $izin  = IzinPenelitian::factory()->create(['status' => 'menunggu']);
        $this->actingAs($admin);

        $this->post(route('admin.izin-penelitian.verifikasi', $izin))->assertRedirect();

        $this->assertDatabaseHas('izin_penelitian', [
            'id'     => $izin->id,
            'status' => 'diverifikasi',
        ]);
    }

    public function test_admin_berhasil_tolak_izin_dengan_catatan(): void
    {
        $admin = $this->makeAdmin();
        $izin  = IzinPenelitian::factory()->create(['status' => 'menunggu']);
        $this->actingAs($admin);

        $this->post(route('admin.izin-penelitian.tolak', $izin), [
            'catatan_verifikasi' => 'Dokumen tidak lengkap.',
        ])->assertRedirect();

        $this->assertDatabaseHas('izin_penelitian', [
            'id'     => $izin->id,
            'status' => 'ditolak',
        ]);
    }

    public function test_admin_tolak_gagal_tanpa_catatan_verifikasi(): void
    {
        $admin = $this->makeAdmin();
        $izin  = IzinPenelitian::factory()->create(['status' => 'menunggu']);
        $this->actingAs($admin);

        $this->post(route('admin.izin-penelitian.tolak', $izin), [])
            ->assertSessionHasErrors(['catatan_verifikasi']);
    }

    public function test_admin_bisa_lihat_detail_izin_penelitian(): void
    {
        $izin = IzinPenelitian::factory()->create();
        $this->actingAs($this->makeAdmin());

        $this->get(route('admin.izin-penelitian.show', $izin))->assertOk();
    }

    // ─────────────────────────────────────────────────────────────
    // KEPALA LAB
    // ─────────────────────────────────────────────────────────────

    public function test_kepala_lab_bisa_lihat_daftar_izin_penelitian(): void
    {
        $this->actingAs($this->makeKepalaLab());

        $this->get(route('kepala_lab.izin-penelitian.index'))->assertOk();
    }

    public function test_admin_diblokir_dari_route_kepala_lab_izin(): void
    {
        $this->actingAs($this->makeAdmin());

        $this->get(route('kepala_lab.izin-penelitian.index'))->assertForbidden();
    }

    public function test_kepala_lab_berhasil_setujui_izin(): void
    {
        $kepala = $this->makeKepalaLab();
        $izin   = IzinPenelitian::factory()->create(['status' => 'diverifikasi']);
        $this->actingAs($kepala);

        $this->post(route('kepala_lab.izin-penelitian.setujui', $izin))->assertRedirect();

        $this->assertDatabaseHas('izin_penelitian', [
            'id'     => $izin->id,
            'status' => 'disetujui',
        ]);
    }

    public function test_kepala_lab_berhasil_tolak_izin_dengan_catatan(): void
    {
        $kepala = $this->makeKepalaLab();
        $izin   = IzinPenelitian::factory()->create(['status' => 'diverifikasi']);
        $this->actingAs($kepala);

        $this->post(route('kepala_lab.izin-penelitian.tolak', $izin), [
            'catatan_verifikasi' => 'Judul tidak sesuai bidang.',
        ])->assertRedirect();

        $this->assertDatabaseHas('izin_penelitian', [
            'id'     => $izin->id,
            'status' => 'ditolak',
        ]);
    }

    public function test_kepala_lab_tolak_gagal_tanpa_catatan_verifikasi(): void
    {
        $kepala = $this->makeKepalaLab();
        $izin   = IzinPenelitian::factory()->create(['status' => 'diverifikasi']);
        $this->actingAs($kepala);

        $this->post(route('kepala_lab.izin-penelitian.tolak', $izin), [])
            ->assertSessionHasErrors(['catatan_verifikasi']);
    }

    public function test_kepala_lab_tidak_bisa_setujui_izin_masih_menunggu(): void
    {
        $kepala = $this->makeKepalaLab();
        $izin   = IzinPenelitian::factory()->create(['status' => 'menunggu']);
        $this->actingAs($kepala);

        $this->post(route('kepala_lab.izin-penelitian.setujui', $izin))->assertRedirect();

        $this->assertDatabaseHas('izin_penelitian', [
            'id'     => $izin->id,
            'status' => 'menunggu',
        ]);
    }
}
