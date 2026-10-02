<?php

namespace Tests\Feature;

use App\Models\ProgramStudi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProgramStudiTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): User
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'admin', 'profile_completed' => true]);
        return $user;
    }

    public function test_admin_can_view_program_studi_index(): void
    {
        $this->actingAs($this->makeAdmin());

        $response = $this->get(route('admin.program-studi.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_create_program_studi(): void
    {
        $this->actingAs($this->makeAdmin());

        $response = $this->post(route('admin.program-studi.store'), [
            'nama_program_studi' => 'Teknik Informatika',
            'jenjang'            => 'S1',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('program_studis', ['nama_program_studi' => 'Teknik Informatika']);
    }

    public function test_admin_cannot_create_duplicate_program_studi(): void
    {
        $this->actingAs($this->makeAdmin());
        ProgramStudi::create(['nama_program_studi' => 'Sistem Informasi', 'jenjang' => 'S1']);

        $response = $this->post(route('admin.program-studi.store'), [
            'nama_program_studi' => 'Sistem Informasi',
            'jenjang'            => 'S1',
        ]);

        $response->assertSessionHasErrors(['nama_program_studi']);
    }

    public function test_admin_can_update_program_studi(): void
    {
        $admin = $this->makeAdmin();
        $ps    = ProgramStudi::create(['nama_program_studi' => 'Prodi Lama', 'jenjang' => 'D3']);
        $this->actingAs($admin);

        $response = $this->put(route('admin.program-studi.update', $ps), [
            'nama_program_studi' => 'Prodi Baru',
            'jenjang'            => 'S1',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('program_studis', ['id' => $ps->id, 'nama_program_studi' => 'Prodi Baru']);
    }

    public function test_admin_can_delete_program_studi(): void
    {
        $admin = $this->makeAdmin();
        $ps    = ProgramStudi::create(['nama_program_studi' => 'Prodi Hapus', 'jenjang' => 'D3']);
        $this->actingAs($admin);

        $response = $this->delete(route('admin.program-studi.destroy', $ps));

        $response->assertRedirect();
        $this->assertDatabaseMissing('program_studis', ['id' => $ps->id]);
    }

    public function test_non_admin_cannot_manage_program_studi(): void
    {
        /** @var User $dosen */
        $dosen = User::factory()->create(['role' => 'dosen']);
        $this->actingAs($dosen);

        $response = $this->get(route('admin.program-studi.index'));

        $response->assertForbidden();
    }
}
