<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KepalaLabDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_kepala_lab_can_access_dashboard(): void
    {
        /** @var User $kepala */
        $kepala = User::factory()->create(['role' => 'kepala_lab', 'profile_completed' => true]);
        $this->actingAs($kepala);

        $response = $this->get(route('kepala_lab.dashboard'));

        $response->assertStatus(200);
    }

    public function test_mahasiswa_cannot_access_kepala_lab_dashboard(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'mahasiswa', 'profile_completed' => true]);
        $this->actingAs($user);

        $response = $this->get(route('kepala_lab.dashboard'));

        $response->assertForbidden();
    }

    public function test_admin_cannot_access_kepala_lab_dashboard(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin', 'profile_completed' => true]);
        $this->actingAs($admin);

        $response = $this->get(route('kepala_lab.dashboard'));

        $response->assertForbidden();
    }

    public function test_dosen_cannot_access_kepala_lab_dashboard(): void
    {
        /** @var User $dosen */
        $dosen = User::factory()->create(['role' => 'dosen', 'profile_completed' => true]);
        $this->actingAs($dosen);

        $response = $this->get(route('kepala_lab.dashboard'));

        $response->assertForbidden();
    }

    public function test_unauthenticated_user_redirected_from_kepala_lab_dashboard(): void
    {
        $response = $this->get(route('kepala_lab.dashboard'));

        $response->assertRedirect(route('login'));
    }
}
