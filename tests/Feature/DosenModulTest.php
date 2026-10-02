<?php

namespace Tests\Feature;

use App\Models\Modul;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DosenModulTest extends TestCase
{
    use RefreshDatabase;

    private function makeDosen(): User
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'dosen', 'profile_completed' => true]);
        return $user;
    }

    public function test_dosen_can_view_modul_index(): void
    {
        $this->actingAs($this->makeDosen());

        $response = $this->get(route('dosen.modul.index'));

        $response->assertStatus(200);
    }

    public function test_dosen_can_upload_modul(): void
    {
        Storage::fake('public');
        $dosen = $this->makeDosen();
        $this->actingAs($dosen);

        $response = $this->post(route('dosen.modul.store'), [
            'nama_modul'  => 'Modul Jaringan Komputer',
            'mata_kuliah' => 'Jaringan Komputer',
            'deskripsi'   => 'Materi pertemuan 1 jaringan komputer.',
            'file'        => UploadedFile::fake()->create('modul.pdf', 500, 'application/pdf'),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('moduls', [
            'user_id'     => $dosen->id,
            'nama_modul'  => 'Modul Jaringan Komputer',
            'mata_kuliah' => 'Jaringan Komputer',
        ]);
    }

    public function test_dosen_store_modul_requires_fields(): void
    {
        $this->actingAs($this->makeDosen());

        $response = $this->post(route('dosen.modul.store'), []);

        $response->assertSessionHasErrors(['nama_modul', 'mata_kuliah', 'file']);
    }

    public function test_dosen_can_delete_own_modul(): void
    {
        Storage::fake('public');
        $dosen = $this->makeDosen();
        $modul = Modul::create([
            'user_id'     => $dosen->id,
            'nama_modul'  => 'Modul Hapus',
            'mata_kuliah' => 'Kuliah A',
            'file_path'   => 'moduls/modul_hapus.pdf',
        ]);
        $this->actingAs($dosen);

        $response = $this->delete(route('dosen.modul.destroy', $modul));

        $response->assertRedirect();
        $this->assertDatabaseMissing('moduls', ['id' => $modul->id]);
    }

    public function test_dosen_cannot_delete_others_modul(): void
    {
        Storage::fake('public');
        $dosen1 = $this->makeDosen();
        $dosen2 = $this->makeDosen();
        $modul  = Modul::create([
            'user_id'     => $dosen2->id,
            'nama_modul'  => 'Modul Milik Dosen2',
            'mata_kuliah' => 'Kuliah B',
            'file_path'   => 'moduls/modul_b.pdf',
        ]);
        $this->actingAs($dosen1);

        $response = $this->delete(route('dosen.modul.destroy', $modul));

        $response->assertForbidden();
    }

    public function test_mahasiswa_cannot_access_modul_management(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'mahasiswa']);
        $this->actingAs($user);

        $response = $this->get(route('dosen.modul.index'));

        $response->assertForbidden();
    }
}
