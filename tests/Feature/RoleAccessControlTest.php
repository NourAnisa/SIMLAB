<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests cross-role access control — ensures each role can ONLY
 * reach their own dashboard and cannot access routes for other roles.
 */
class RoleAccessControlTest extends TestCase
{
    use RefreshDatabase;

    // ── Dashboard redirect ──────────────────────────────────────

    public function test_dashboard_redirects_admin_to_admin_dashboard(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $response = $this->get('/dashboard');

        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_dashboard_redirects_kepala_lab_to_kepala_lab_dashboard(): void
    {
        /** @var User $kepala */
        $kepala = User::factory()->create(['role' => 'kepala_lab']);
        $this->actingAs($kepala);

        $response = $this->get('/dashboard');

        $response->assertRedirect(route('kepala_lab.dashboard'));
    }

    public function test_dashboard_redirects_dosen_to_dosen_dashboard(): void
    {
        /** @var User $dosen */
        $dosen = User::factory()->create(['role' => 'dosen']);
        $this->actingAs($dosen);

        $response = $this->get('/dashboard');

        $response->assertRedirect(route('dosen.dashboard'));
    }

    public function test_dashboard_redirects_mahasiswa_to_mahasiswa_dashboard(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'mahasiswa']);
        $this->actingAs($user);

        $response = $this->get('/dashboard');

        $response->assertRedirect(route('mahasiswa.dashboard'));
    }

    // ── Mahasiswa cannot access other role routes ───────────────

    public function test_mahasiswa_cannot_access_admin_routes(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'mahasiswa']);
        $this->actingAs($user);

        $this->get(route('admin.dashboard'))->assertForbidden();
        $this->get(route('admin.users.index'))->assertForbidden();
        $this->get(route('admin.ruangan.index'))->assertForbidden();
        $this->get(route('admin.skema.index'))->assertForbidden();
    }

    public function test_mahasiswa_cannot_access_kepala_lab_routes(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'mahasiswa']);
        $this->actingAs($user);

        $this->get(route('kepala_lab.dashboard'))->assertForbidden();
        $this->get(route('kepala_lab.sertifikasi.index'))->assertForbidden();
        $this->get(route('kepala_lab.izin-penelitian.index'))->assertForbidden();
    }

    public function test_mahasiswa_cannot_access_dosen_routes(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'mahasiswa']);
        $this->actingAs($user);

        $this->get(route('dosen.dashboard'))->assertForbidden();
        $this->get(route('dosen.modul.index'))->assertForbidden();
        $this->get(route('dosen.peminjaman-lab.index'))->assertForbidden();
    }

    // ── Admin cannot access other role dashboards ───────────────

    public function test_admin_cannot_access_kepala_lab_dashboard(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $this->get(route('kepala_lab.dashboard'))->assertForbidden();
    }

    public function test_admin_cannot_access_dosen_dashboard(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $this->get(route('dosen.dashboard'))->assertForbidden();
    }

    public function test_admin_cannot_access_mahasiswa_dashboard(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $this->get(route('mahasiswa.dashboard'))->assertForbidden();
    }

    // ── Dosen cannot access other role routes ───────────────────

    public function test_dosen_cannot_access_admin_routes(): void
    {
        /** @var User $dosen */
        $dosen = User::factory()->create(['role' => 'dosen']);
        $this->actingAs($dosen);

        $this->get(route('admin.dashboard'))->assertForbidden();
        $this->get(route('admin.users.index'))->assertForbidden();
    }

    public function test_dosen_cannot_access_kepala_lab_routes(): void
    {
        /** @var User $dosen */
        $dosen = User::factory()->create(['role' => 'dosen']);
        $this->actingAs($dosen);

        $this->get(route('kepala_lab.dashboard'))->assertForbidden();
        $this->get(route('kepala_lab.sertifikasi.index'))->assertForbidden();
    }

    // ── Guest access ─────────────────────────────────────────────

    public function test_guest_cannot_access_any_protected_route(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
        $this->get(route('kepala_lab.dashboard'))->assertRedirect(route('login'));
        $this->get(route('dosen.dashboard'))->assertRedirect(route('login'));
        $this->get(route('mahasiswa.dashboard'))->assertRedirect(route('login'));
    }
}
