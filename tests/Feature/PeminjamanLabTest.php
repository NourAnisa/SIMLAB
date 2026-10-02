<?php

namespace Tests\Feature;

use App\Models\PeminjamanLab;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

/**
 * Feature: Peminjaman Lab — alur lengkap semua role.
 *
 * Status flow:
 *   menunggu → (admin) diverifikasi / ditolak
 *            → (kepala_lab) disetujui / ditolak
 *            → (mahasiswa/dosen) menunggu_kembali
 *            → (admin) dikembalikan
 */
class PeminjamanLabTest extends TestCase
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

    private function makeDosen(): User
    {
        return User::factory()->create(['role' => 'dosen']);
    }

    private function makeAdmin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function makeKepalaLab(): User
    {
        return User::factory()->create(['role' => 'kepala_lab']);
    }

    /** Payload valid untuk store peminjaman (ruangan adalah string nama). */
    private function payload(): array
    {
        return [
            'ruangan'        => 'Lab Komputer A',
            'tanggal_pinjam' => now()->addDay()->toDateString(),
            'jam_mulai'      => '08:00',
            'jam_selesai'    => '10:00',
            'keperluan'      => 'Praktikum',
        ];
    }

    // ─────────────────────────────────────────────────────────────
    // MAHASISWA
    // ─────────────────────────────────────────────────────────────

    public function test_mahasiswa_bisa_lihat_halaman_peminjaman(): void
    {
        $this->actingAs($this->makeMahasiswa());

        $this->get(route('mahasiswa.peminjaman-lab.index'))->assertOk();
    }

    public function test_mahasiswa_profil_belum_lengkap_diredirect(): void
    {
        $this->actingAs($this->makeMahasiswa(false));

        $this->get(route('mahasiswa.peminjaman-lab.index'))
            ->assertRedirect(route('profile.edit'));
    }

    public function test_tamu_diredirect_ke_login(): void
    {
        $this->get(route('mahasiswa.peminjaman-lab.index'))
            ->assertRedirect(route('login'));
    }

    public function test_mahasiswa_berhasil_buat_peminjaman(): void
    {
        $user = $this->makeMahasiswa();
        $this->actingAs($user);

        $this->post(route('mahasiswa.peminjaman-lab.store'), $this->payload())
            ->assertRedirect();

        $this->assertDatabaseHas('peminjaman_lab', [
            'user_id'  => $user->id,
            'ruangan'  => 'Lab Komputer A',
            'status'   => 'menunggu',
        ]);
    }

    public function test_mahasiswa_peminjaman_gagal_tanpa_keperluan(): void
    {
        $user = $this->makeMahasiswa();
        $this->actingAs($user);

        $payload = $this->payload();
        unset($payload['keperluan']);

        $this->post(route('mahasiswa.peminjaman-lab.store'), $payload)
            ->assertSessionHasErrors(['keperluan']);
    }

    public function test_mahasiswa_bisa_lihat_detail_peminjaman_sendiri(): void
    {
        $user       = $this->makeMahasiswa();
        $peminjaman = PeminjamanLab::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $this->get(route('mahasiswa.peminjaman-lab.show', $peminjaman))->assertOk();
    }

    public function test_mahasiswa_tidak_bisa_lihat_peminjaman_orang_lain(): void
    {
        $user1      = $this->makeMahasiswa();
        $user2      = $this->makeMahasiswa();
        $peminjaman = PeminjamanLab::factory()->create(['user_id' => $user2->id]);
        $this->actingAs($user1);

        $this->get(route('mahasiswa.peminjaman-lab.show', $peminjaman))->assertForbidden();
    }

    public function test_mahasiswa_bisa_kembalikan_peminjaman_disetujui(): void
    {
        $user       = $this->makeMahasiswa();
        $peminjaman = PeminjamanLab::factory()->create(['user_id' => $user->id, 'status' => 'disetujui']);
        $this->actingAs($user);

        $this->post(route('mahasiswa.peminjaman-lab.kembalikan', $peminjaman))->assertRedirect();

        $this->assertDatabaseHas('peminjaman_lab', [
            'id'     => $peminjaman->id,
            'status' => 'menunggu_kembali',
        ]);
    }

    public function test_mahasiswa_tidak_bisa_kembalikan_milik_orang_lain(): void
    {
        $user1      = $this->makeMahasiswa();
        $user2      = $this->makeMahasiswa();
        $peminjaman = PeminjamanLab::factory()->create(['user_id' => $user2->id, 'status' => 'disetujui']);
        $this->actingAs($user1);

        $this->post(route('mahasiswa.peminjaman-lab.kembalikan', $peminjaman))->assertForbidden();
    }

    public function test_mahasiswa_cetak_bukti_pinjam_jika_disetujui(): void
    {
        $user       = $this->makeMahasiswa();
        $peminjaman = PeminjamanLab::factory()->create(['user_id' => $user->id, 'status' => 'disetujui']);
        $this->actingAs($user);

        $this->get(route('mahasiswa.peminjaman-lab.cetak-bukti-pinjam', $peminjaman))->assertOk();
    }

    public function test_mahasiswa_cetak_bukti_kembali_jika_dikembalikan(): void
    {
        $user       = $this->makeMahasiswa();
        $peminjaman = PeminjamanLab::factory()->create(['user_id' => $user->id, 'status' => 'dikembalikan']);
        $this->actingAs($user);

        $this->get(route('mahasiswa.peminjaman-lab.cetak-bukti-kembali', $peminjaman))->assertOk();
    }

    // ─────────────────────────────────────────────────────────────
    // DOSEN
    // ─────────────────────────────────────────────────────────────

    public function test_dosen_bisa_lihat_halaman_peminjaman(): void
    {
        $this->actingAs($this->makeDosen());

        $this->get(route('dosen.peminjaman-lab.index'))->assertOk();
    }

    public function test_mahasiswa_diblokir_dari_route_dosen_peminjaman(): void
    {
        $this->actingAs($this->makeMahasiswa());

        $this->get(route('dosen.peminjaman-lab.index'))->assertForbidden();
    }

    public function test_dosen_berhasil_buat_peminjaman(): void
    {
        $dosen = $this->makeDosen();
        $this->actingAs($dosen);

        $this->post(route('dosen.peminjaman-lab.store'), $this->payload())
            ->assertRedirect();

        $this->assertDatabaseHas('peminjaman_lab', [
            'user_id' => $dosen->id,
            'ruangan' => 'Lab Komputer A',
            'status'  => 'menunggu',
        ]);
    }

    public function test_dosen_tidak_bisa_lihat_peminjaman_orang_lain(): void
    {
        $dosen1     = $this->makeDosen();
        $dosen2     = $this->makeDosen();
        $peminjaman = PeminjamanLab::factory()->create(['user_id' => $dosen2->id]);
        $this->actingAs($dosen1);

        $this->get(route('dosen.peminjaman-lab.show', $peminjaman))->assertForbidden();
    }

    public function test_dosen_bisa_kembalikan_peminjaman_sendiri(): void
    {
        $dosen      = $this->makeDosen();
        $peminjaman = PeminjamanLab::factory()->create(['user_id' => $dosen->id, 'status' => 'disetujui']);
        $this->actingAs($dosen);

        $this->patch(route('dosen.peminjaman-lab.kembalikan', $peminjaman))->assertRedirect();

        $this->assertDatabaseHas('peminjaman_lab', [
            'id'     => $peminjaman->id,
            'status' => 'menunggu_kembali',
        ]);
    }

    public function test_dosen_tidak_bisa_kembalikan_milik_orang_lain(): void
    {
        $dosen1     = $this->makeDosen();
        $dosen2     = $this->makeDosen();
        $peminjaman = PeminjamanLab::factory()->create(['user_id' => $dosen2->id, 'status' => 'disetujui']);
        $this->actingAs($dosen1);

        $this->patch(route('dosen.peminjaman-lab.kembalikan', $peminjaman))->assertForbidden();
    }

    // ─────────────────────────────────────────────────────────────
    // ADMIN
    // ─────────────────────────────────────────────────────────────

    public function test_admin_bisa_lihat_daftar_peminjaman(): void
    {
        $this->actingAs($this->makeAdmin());

        $this->get(route('admin.peminjaman-lab.index'))->assertOk();
    }

    public function test_non_admin_diblokir_dari_halaman_admin_peminjaman(): void
    {
        $this->actingAs($this->makeDosen());

        $this->get(route('admin.peminjaman-lab.index'))->assertForbidden();
    }

    public function test_admin_berhasil_verifikasi_peminjaman(): void
    {
        $admin      = $this->makeAdmin();
        $peminjaman = PeminjamanLab::factory()->create(['status' => 'menunggu']);
        $this->actingAs($admin);

        $this->post(route('admin.peminjaman-lab.verifikasi', $peminjaman))->assertRedirect();

        $this->assertDatabaseHas('peminjaman_lab', [
            'id'     => $peminjaman->id,
            'status' => 'diverifikasi',
        ]);
    }

    public function test_admin_berhasil_tolak_peminjaman(): void
    {
        $admin      = $this->makeAdmin();
        $peminjaman = PeminjamanLab::factory()->create(['status' => 'menunggu']);
        $this->actingAs($admin);

        $this->post(route('admin.peminjaman-lab.tolak', $peminjaman))->assertRedirect();

        $this->assertDatabaseHas('peminjaman_lab', [
            'id'     => $peminjaman->id,
            'status' => 'ditolak',
        ]);
    }

    public function test_admin_berhasil_verifikasi_kembali_peminjaman(): void
    {
        $admin      = $this->makeAdmin();
        $peminjaman = PeminjamanLab::factory()->create(['status' => 'menunggu_kembali']);
        $this->actingAs($admin);

        $this->post(route('admin.peminjaman-lab.verifikasi-kembali', $peminjaman))->assertRedirect();

        $this->assertDatabaseHas('peminjaman_lab', [
            'id'     => $peminjaman->id,
            'status' => 'dikembalikan',
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // KEPALA LAB
    // ─────────────────────────────────────────────────────────────

    public function test_kepala_lab_bisa_lihat_daftar_peminjaman(): void
    {
        $this->actingAs($this->makeKepalaLab());

        $this->get(route('kepala_lab.peminjaman-lab.index'))->assertOk();
    }

    public function test_dosen_diblokir_dari_route_kepala_lab_peminjaman(): void
    {
        $this->actingAs($this->makeDosen());

        $this->get(route('kepala_lab.peminjaman-lab.index'))->assertForbidden();
    }

    public function test_kepala_lab_berhasil_setujui_peminjaman(): void
    {
        $kepala     = $this->makeKepalaLab();
        $peminjaman = PeminjamanLab::factory()->create(['status' => 'diverifikasi']);
        $this->actingAs($kepala);

        $this->post(route('kepala_lab.peminjaman-lab.setujui', $peminjaman))->assertRedirect();

        $this->assertDatabaseHas('peminjaman_lab', [
            'id'     => $peminjaman->id,
            'status' => 'disetujui',
        ]);
    }

    public function test_kepala_lab_berhasil_tolak_peminjaman(): void
    {
        $kepala     = $this->makeKepalaLab();
        $peminjaman = PeminjamanLab::factory()->create(['status' => 'diverifikasi']);
        $this->actingAs($kepala);

        $this->post(route('kepala_lab.peminjaman-lab.tolak', $peminjaman))->assertRedirect();

        $this->assertDatabaseHas('peminjaman_lab', [
            'id'     => $peminjaman->id,
            'status' => 'ditolak',
        ]);
    }

    public function test_kepala_lab_tidak_bisa_setujui_peminjaman_masih_menunggu(): void
    {
        $kepala     = $this->makeKepalaLab();
        $peminjaman = PeminjamanLab::factory()->create(['status' => 'menunggu']);
        $this->actingAs($kepala);

        $this->post(route('kepala_lab.peminjaman-lab.setujui', $peminjaman))->assertRedirect();

        $this->assertDatabaseHas('peminjaman_lab', [
            'id'     => $peminjaman->id,
            'status' => 'menunggu',
        ]);
    }
}

