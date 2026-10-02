<?php

namespace Tests\Unit\Controllers\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticatedSessionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_shows_the_login_page()
    {
        $response = $this->get(route('login'));
        $response->assertStatus(200);
    }

    public function test_it_fails_login_with_wrong_credentials()
    {
        $response = $this->post(route('login'), [
            'email'    => 'salah@stmik.ac.id',
            'password' => 'wrongpassword',
        ]);
        $response->assertSessionHasErrors(['email']);
    }

    public function test_it_logs_in_as_admin_and_redirects_to_dashboard()
    {
        $user = User::factory()->create([
            'email'    => 'admin@stmik.ac.id',
            'password' => bcrypt('password123'),
            'role'     => 'admin',
        ]);

        $response = $this->post(route('login'), [
            'email'    => 'admin@stmik.ac.id',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
        $this->get('/dashboard')->assertRedirect('/admin/dashboard');
    }

    public function test_it_logs_in_as_mahasiswa_and_redirects_to_dashboard()
    {
        $user = User::factory()->create([
            'email'    => 'mahasiswa@mhs.stmik.ac.id',
            'password' => bcrypt('password123'),
            'role'     => 'mahasiswa',
        ]);

        $response = $this->post(route('login'), [
            'email'    => 'mahasiswa@mhs.stmik.ac.id',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
        $this->get('/dashboard')->assertRedirect('/mahasiswa/dashboard');
    }

    public function test_it_logs_in_as_kepala_lab_and_redirects_to_dashboard()
    {
        $user = User::factory()->create([
            'email'    => 'kepala@stmik.ac.id',
            'password' => bcrypt('password123'),
            'role'     => 'kepala_lab',
        ]);

        $response = $this->post(route('login'), [
            'email'    => 'kepala@stmik.ac.id',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
        $this->get('/dashboard')->assertRedirect('/kepala_lab/dashboard');
    }

    public function test_it_logs_in_as_dosen_and_redirects_to_dashboard()
    {
        $user = User::factory()->create([
            'email'    => 'dosen@stmik.ac.id',
            'password' => bcrypt('password123'),
            'role'     => 'dosen',
        ]);

        $response = $this->post(route('login'), [
            'email'    => 'dosen@stmik.ac.id',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
        $this->get('/dashboard')->assertRedirect('/dosen/dashboard');
    }

    public function test_it_logs_out_and_redirects_to_login()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('logout'));

        $response->assertRedirect('/');
        $this->assertGuest();
    }
}
