<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): User
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'admin', 'profile_completed' => true]);
        return $user;
    }

    public function test_admin_can_view_user_list(): void
    {
        $this->actingAs($this->makeAdmin());

        $response = $this->get(route('admin.users.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_create_new_user(): void
    {
        $this->actingAs($this->makeAdmin());

        $response = $this->post(route('admin.users.store'), [
            'name'                  => 'Mahasiswa Baru',
            'email'                 => 'mhs.baru@gmail.com',
            'role'                  => 'mahasiswa',
            'nim_nip'               => '20210001',
            'jurusan'               => 'Teknik Informatika',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['email' => 'mhs.baru@gmail.com', 'role' => 'mahasiswa']);
    }

    public function test_admin_store_user_requires_required_fields(): void
    {
        $this->actingAs($this->makeAdmin());

        $response = $this->post(route('admin.users.store'), []);

        $response->assertSessionHasErrors(['name', 'email', 'role', 'password']);
    }

    public function test_admin_can_update_user(): void
    {
        $admin  = $this->makeAdmin();
        /** @var User $target */
        $target = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Nama Lama']);
        $this->actingAs($admin);

        $response = $this->put(route('admin.users.update', $target), [
            'name'   => 'Nama Baru',
            'email'  => $target->email,
            'role'   => 'mahasiswa',
            'nim_nip' => '12345',
            'jurusan' => 'Sistem Informasi',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $target->id, 'name' => 'Nama Baru']);
    }

    public function test_admin_can_delete_another_user(): void
    {
        $admin  = $this->makeAdmin();
        /** @var User $target */
        $target = User::factory()->create(['role' => 'mahasiswa']);
        $this->actingAs($admin);

        $response = $this->delete(route('admin.users.destroy', $target));

        $response->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $target->id]);
    }

    public function test_admin_cannot_delete_own_account(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin);

        $response = $this->delete(route('admin.users.destroy', $admin));

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_mahasiswa_cannot_access_user_management(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'mahasiswa']);
        $this->actingAs($user);

        $response = $this->get(route('admin.users.index'));

        $response->assertForbidden();
    }
}
