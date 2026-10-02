<?php

namespace Tests\Feature;

use App\Models\PeminjamanLab;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MahasiswaSuratBebasLabTest extends TestCase
{
    use RefreshDatabase;

    public function test_mahasiswa_can_submit_surat_bebas_lab_when_no_active_loan_exists(): void
    {
        $user = $this->createCompletedMahasiswa();

        Sanctum::actingAs($user);

        $this->postJson('/api/mahasiswa/surat-bebas-lab')
            ->assertCreated()
            ->assertJsonPath('message', 'Permohonan surat bebas lab berhasil diajukan.');

        $this->assertDatabaseHas('surat_bebas_lab', [
            'user_id' => $user->id,
            'status' => 'menunggu',
        ]);
    }

    public function test_active_loan_returns_validation_error_when_submitting_surat_bebas_lab(): void
    {
        $user = $this->createCompletedMahasiswa();

        PeminjamanLab::create([
            'user_id' => $user->id,
            'ruangan' => 'Lab Komputer 1',
            'tanggal_pinjam' => now()->toDateString(),
            'jam_mulai' => '08:00',
            'jam_selesai' => '10:00',
            'keperluan' => 'Praktikum',
            'status' => 'disetujui',
        ]);

        Sanctum::actingAs($user);

        $this->postJson('/api/mahasiswa/surat-bebas-lab')
            ->assertStatus(422)
            ->assertJsonPath('message', 'Anda masih memiliki tanggungan peminjaman lab aktif. Harap kembalikan barang/ruangan sebelum mengajukan surat bebas lab.');
    }

    public function test_incomplete_mahasiswa_profile_is_blocked_from_api_features(): void
    {
        $user = User::factory()->create([
            'role' => 'mahasiswa',
            'profile_completed' => false,
            'nim_nip' => null,
            'jurusan' => null,
            'foto_profil' => null,
        ]);

        Sanctum::actingAs($user);

        $this->getJson('/api/mahasiswa/sertifikasi')
            ->assertForbidden()
            ->assertJsonPath('message', 'Profile belum lengkap')
            ->assertJsonPath('missing_fields.0', 'NIM (Nomor Induk Mahasiswa)');
    }

    public function test_surat_bebas_lab_print_routes_use_single_prefix_names(): void
    {
        $this->assertTrue(Route::has('mahasiswa.surat-bebas-lab.cetak'));
        $this->assertTrue(Route::has('kepala_lab.surat-bebas-lab.cetak'));
        $this->assertFalse(Route::has('mahasiswa.mahasiswa.surat-bebas-lab.cetak'));
        $this->assertFalse(Route::has('kepala_lab.kepala_lab.surat-bebas-lab.cetak'));
    }

    private function createCompletedMahasiswa(): User
    {
        return User::factory()->create([
            'role' => 'mahasiswa',
            'nim_nip' => '123456',
            'jurusan' => 'Teknik Informatika',
            'foto_profil' => 'foto_profil/test.jpg',
            'profile_completed' => true,
        ]);
    }
}
