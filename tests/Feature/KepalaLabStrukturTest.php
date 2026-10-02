<?php

namespace Tests\Feature;

use App\Models\StrukturOrganisasi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class KepalaLabStrukturTest extends TestCase
{
    use RefreshDatabase;

    private function makeKepalaLab(): User
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'kepala_lab', 'profile_completed' => true]);
        return $user;
    }

    private function makeAdmin(): User
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'admin', 'profile_completed' => true]);
        return $user;
    }

    public function test_kepala_lab_can_view_struktur_index(): void
    {
        $this->actingAs($this->makeKepalaLab());

        $response = $this->get(route('kepala_lab.struktur.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_view_struktur_index(): void
    {
        $this->actingAs($this->makeAdmin());

        $response = $this->get(route('kepala_lab.struktur.index'));

        $response->assertStatus(200);
    }

    public function test_kepala_lab_can_store_struktur(): void
    {
        Storage::fake('public');
        $this->actingAs($this->makeKepalaLab());

        $response = $this->post(route('kepala_lab.struktur.store'), [
            'nama'    => 'Dr. John Doe',
            'jabatan' => 'Kepala Laboratorium',
            'urutan'  => 1,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('struktur_organisasi', [
            'nama'    => 'Dr. John Doe',
            'jabatan' => 'Kepala Laboratorium',
        ]);
    }

    public function test_kepala_lab_store_struktur_requires_fields(): void
    {
        $this->actingAs($this->makeKepalaLab());

        $response = $this->post(route('kepala_lab.struktur.store'), []);

        $response->assertSessionHasErrors(['nama', 'jabatan']);
    }

    public function test_kepala_lab_can_update_struktur(): void
    {
        Storage::fake('public');
        $kepala   = $this->makeKepalaLab();
        $struktur = StrukturOrganisasi::create([
            'nama'    => 'Nama Lama',
            'jabatan' => 'Jabatan Lama',
            'urutan'  => 1,
        ]);
        $this->actingAs($kepala);

        $response = $this->put(route('kepala_lab.struktur.update', $struktur), [
            'nama'    => 'Nama Baru',
            'jabatan' => 'Jabatan Baru',
            'urutan'  => 2,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('struktur_organisasi', [
            'id'     => $struktur->id,
            'nama'   => 'Nama Baru',
        ]);
    }

    public function test_kepala_lab_can_delete_struktur(): void
    {
        Storage::fake('public');
        $kepala   = $this->makeKepalaLab();
        $struktur = StrukturOrganisasi::create([
            'nama'    => 'Nama Hapus',
            'jabatan' => 'Jabatan Hapus',
        ]);
        $this->actingAs($kepala);

        $response = $this->delete(route('kepala_lab.struktur.destroy', $struktur));

        $response->assertRedirect();
        $this->assertDatabaseMissing('struktur_organisasi', ['id' => $struktur->id]);
    }

    public function test_mahasiswa_cannot_manage_struktur(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'mahasiswa']);
        $this->actingAs($user);

        $response = $this->get(route('kepala_lab.struktur.index'));

        $response->assertForbidden();
    }
}
