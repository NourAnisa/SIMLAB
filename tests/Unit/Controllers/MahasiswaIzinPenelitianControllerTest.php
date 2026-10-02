<?php

namespace Tests\Unit\Controllers;

use App\Models\User;
use App\Models\IzinPenelitian;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MahasiswaIzinPenelitianControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_mahasiswa_can_view_izin_list()
    {
        $user = User::factory()->create(['role' => 'mahasiswa', 'profile_completed' => true]);
        $this->actingAs($user);

        $response = $this->get(route('mahasiswa.izin-penelitian.index'));

        $response->assertStatus(200);
    }

    public function test_mahasiswa_can_submit_izin_penelitian()
    {
        $user = User::factory()->create(['role' => 'mahasiswa', 'profile_completed' => true]);
        $this->actingAs($user);

        $response = $this->post(route('mahasiswa.izin-penelitian.store'), [
            'judul_penelitian' => 'Analisis Keamanan Jaringan Komputer',
            'tujuan'           => 'Menyelesaikan penelitian Tugas Akhir di laboratorium',
            'data_dibutuhkan'  => ['Router', 'Switch', 'PC'],
        ]);

        $response->assertRedirect(route('mahasiswa.izin-penelitian.index'));
        $this->assertDatabaseHas('izin_penelitian', [
            'user_id'          => $user->id,
            'judul_penelitian' => 'Analisis Keamanan Jaringan Komputer',
            'status'           => 'menunggu',
        ]);
    }

    public function test_mahasiswa_cannot_view_other_izin()
    {
        $user1 = User::factory()->create(['role' => 'mahasiswa', 'profile_completed' => true]);
        $user2 = User::factory()->create(['role' => 'mahasiswa', 'profile_completed' => true]);
        $izin  = IzinPenelitian::factory()->create(['user_id' => $user2->id]);
        $this->actingAs($user1);

        $response = $this->get(route('mahasiswa.izin-penelitian.show', $izin));

        $response->assertForbidden();
    }

    public function test_cetak_blocked_if_not_disetujui()
    {
        $user = User::factory()->create(['role' => 'mahasiswa', 'profile_completed' => true]);
        $izin = IzinPenelitian::factory()->create([
            'user_id' => $user->id,
            'status'  => 'menunggu',
        ]);
        $this->actingAs($user);

        $response = $this->get(route('mahasiswa.izin-penelitian.cetak', $izin));

        $response->assertForbidden();
    }

    public function test_admin_can_verifikasi_izin()
    {
        $admin = User::factory()->create(['role' => 'admin', 'profile_completed' => true]);
        $izin  = IzinPenelitian::factory()->create(['status' => 'menunggu']);
        $this->actingAs($admin);

        $response = $this->post(route('admin.izin-penelitian.verifikasi', $izin));

        $response->assertRedirect();
        $this->assertDatabaseHas('izin_penelitian', [
            'id'     => $izin->id,
            'status' => 'diverifikasi',
        ]);
    }

    public function test_kepala_lab_can_setujui_izin()
    {
        $kepala = User::factory()->create(['role' => 'kepala_lab', 'profile_completed' => true]);
        $izin   = IzinPenelitian::factory()->create(['status' => 'diverifikasi']);
        $this->actingAs($kepala);

        $response = $this->post(route('kepala_lab.izin-penelitian.setujui', $izin));

        $response->assertRedirect();
        $this->assertDatabaseHas('izin_penelitian', [
            'id'     => $izin->id,
            'status' => 'disetujui',
        ]);
    }
}
