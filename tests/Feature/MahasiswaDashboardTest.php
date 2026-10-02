<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MahasiswaDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_incomplete_mahasiswa_dashboard_api_reports_locked_state(): void
    {
        $user = User::factory()->create([
            'role' => 'mahasiswa',
            'profile_completed' => false,
            'nim_nip' => null,
            'jurusan' => null,
            'foto_profil' => null,
        ]);

        Sanctum::actingAs($user);

        $this->getJson('/api/mahasiswa/dashboard')
            ->assertOk()
            ->assertJsonPath('profileCheck.can_access', false)
            ->assertJsonPath('profileCheck.missing_fields.0', 'NIM (Nomor Induk Mahasiswa)')
            ->assertJsonPath('profileCheck.missing_fields.2', 'Foto Profil');
    }

    public function test_completed_mahasiswa_dashboard_api_reports_unlocked_state(): void
    {
        $user = User::factory()->create([
            'role' => 'mahasiswa',
            'nim_nip' => '123456',
            'jurusan' => 'Teknik Informatika',
            'foto_profil' => 'foto_profil/test.jpg',
            'profile_completed' => true,
        ]);

        Sanctum::actingAs($user);

        $this->getJson('/api/mahasiswa/dashboard')
            ->assertOk()
            ->assertJsonPath('profileCheck.can_access', true)
            ->assertJsonCount(0, 'profileCheck.missing_fields');
    }
}
