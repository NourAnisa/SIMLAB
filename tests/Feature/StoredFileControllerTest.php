<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StoredFileControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_users_can_view_files_from_public_storage(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('private-docs/test.txt', 'secret');

        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/files/private-docs/test.txt')
            ->assertOk();
    }

    public function test_authenticated_file_route_blocks_path_traversal_attempts(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/files/../../../.env')
            ->assertNotFound();
    }

    public function test_public_file_route_serves_whitelisted_folders_only(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('private/secret.txt', 'secret');

        $this->get('/storage-public/private/secret.txt')
            ->assertNotFound();
    }

    public function test_public_file_route_blocks_path_traversal_attempts(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('berita/publik.txt', 'ok');

        $this->get('/storage-public/berita/../../../../.env')
            ->assertNotFound();
    }

    public function test_public_file_route_serves_files_from_whitelisted_folders(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('berita/publik.txt', 'ok');

        $this->get('/storage-public/berita/publik.txt')
            ->assertOk();
    }
}
