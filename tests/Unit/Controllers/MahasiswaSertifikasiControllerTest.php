<?php

namespace Tests\Unit\Controllers;

use App\Models\User;
use App\Models\JadwalSertifikasi;
use App\Models\PendaftaranSertifikasi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MahasiswaSertifikasiControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_mahasiswa_can_view_jadwal_sertifikasi()
    {
        $user = User::factory()->create(['role' => 'mahasiswa', 'profile_completed' => true]);
        $this->actingAs($user);

        $response = $this->get(route('mahasiswa.sertifikasi.index'));

        $response->assertStatus(200);
    }

    public function test_mahasiswa_can_view_riwayat_sertifikasi()
    {
        $user = User::factory()->create(['role' => 'mahasiswa', 'profile_completed' => true]);
        $this->actingAs($user);

        $response = $this->get(route('mahasiswa.sertifikasi.riwayat'));

        $response->assertStatus(200);
    }

    public function test_mahasiswa_can_register_to_jadwal()
    {
        Storage::fake('public');

        $user   = User::factory()->create(['role' => 'mahasiswa', 'profile_completed' => true]);
        $jadwal = JadwalSertifikasi::factory()->create(['kuota' => 10]);
        $this->actingAs($user);

        $response = $this->post(route('mahasiswa.sertifikasi.daftar.store', $jadwal), [
            'dokumen_berkas' => UploadedFile::fake()->create('berkas.pdf', 100, 'application/pdf'),
        ]);

        $response->assertRedirect(route('mahasiswa.sertifikasi.riwayat'));
        $this->assertDatabaseHas('pendaftaran_sertifikasi', [
            'user_id'   => $user->id,
            'jadwal_id' => $jadwal->id,
            'status'    => 'menunggu',
        ]);
    }

    public function test_admin_can_verify_pendaftaran()
    {
        $admin       = User::factory()->create(['role' => 'admin', 'profile_completed' => true]);
        $pendaftaran = PendaftaranSertifikasi::factory()->create(['status' => 'menunggu']);
        $this->actingAs($admin);

        $response = $this->post(route('admin.sertifikasi.verifikasi', $pendaftaran));

        $response->assertRedirect();
        $this->assertDatabaseHas('pendaftaran_sertifikasi', [
            'id'     => $pendaftaran->id,
            'status' => 'diverifikasi',
        ]);
    }

    public function test_admin_can_mark_as_lulus()
    {
        $admin       = User::factory()->create(['role' => 'admin', 'profile_completed' => true]);
        $pendaftaran = PendaftaranSertifikasi::factory()->create(['status' => 'diverifikasi']);
        $this->actingAs($admin);

        $response = $this->post(route('admin.sertifikasi.lulus', $pendaftaran));

        $response->assertRedirect();
        $this->assertDatabaseHas('pendaftaran_sertifikasi', [
            'id'     => $pendaftaran->id,
            'status' => 'lulus',
        ]);
    }

    public function test_admin_can_mark_as_tidak_lulus()
    {
        $admin       = User::factory()->create(['role' => 'admin', 'profile_completed' => true]);
        $pendaftaran = PendaftaranSertifikasi::factory()->create(['status' => 'diverifikasi']);
        $this->actingAs($admin);

        $response = $this->post(route('admin.sertifikasi.tidak-lulus', $pendaftaran));

        $response->assertRedirect();
        $this->assertDatabaseHas('pendaftaran_sertifikasi', [
            'id'     => $pendaftaran->id,
            'status' => 'tidak_lulus',
        ]);
    }

    public function test_cetak_blocked_if_status_not_lulus()
    {
        $user        = User::factory()->create(['role' => 'mahasiswa', 'profile_completed' => true]);
        $pendaftaran = PendaftaranSertifikasi::factory()->create([
            'user_id' => $user->id,
            'status'  => 'menunggu',
        ]);
        $this->actingAs($user);

        $response = $this->get(route('mahasiswa.sertifikasi.cetak', $pendaftaran));

        $response->assertForbidden();
    }
}
