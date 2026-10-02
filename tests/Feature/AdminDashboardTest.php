<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_dashboard(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin', 'profile_completed' => true]);
        $this->actingAs($admin);

        $response = $this->get(route('admin.dashboard'));

        $response->assertStatus(200);
    }

    public function test_mahasiswa_cannot_access_admin_dashboard(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'mahasiswa', 'profile_completed' => true]);
        $this->actingAs($user);

        $response = $this->get(route('admin.dashboard'));

        $response->assertForbidden();
    }

    public function test_dosen_cannot_access_admin_dashboard(): void
    {
        /** @var User $dosen */
        $dosen = User::factory()->create(['role' => 'dosen', 'profile_completed' => true]);
        $this->actingAs($dosen);

        $response = $this->get(route('admin.dashboard'));

        $response->assertForbidden();
    }

    public function test_kepala_lab_cannot_access_admin_dashboard(): void
    {
        /** @var User $kepala */
        $kepala = User::factory()->create(['role' => 'kepala_lab', 'profile_completed' => true]);
        $this->actingAs($kepala);

        $response = $this->get(route('admin.dashboard'));

        $response->assertForbidden();
    }

    public function test_unauthenticated_user_redirected_from_admin_dashboard(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('login'));
    }
}
