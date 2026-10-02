<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class StoredFileController extends Controller
{
    private const PUBLIC_FOLDERS = ['skema_logos', 'berita', 'struktur', 'site', 'moduls'];

    public function servePrivate(string $path): BinaryFileResponse
    {
        return $this->serve($path);
    }

    public function servePublic(string $path): BinaryFileResponse
    {
        $normalizedPath = $this->normalizePath($path);
        $rootFolder = explode('/', $normalizedPath, 2)[0] ?? '';

        abort_unless(in_array($rootFolder, self::PUBLIC_FOLDERS, true), 404);

        return $this->serve($normalizedPath);
    }

    private function serve(string $path): BinaryFileResponse
    {
        $normalizedPath = $this->normalizePath($path);
        $disk = Storage::disk('public');

        abort_unless($disk->exists($normalizedPath), 404);

        $basePath = realpath($disk->path(''));
        $absolutePath = realpath($disk->path($normalizedPath));

        abort_unless(
            $basePath !== false
                && $absolutePath !== false
                && str_starts_with($absolutePath, $basePath . DIRECTORY_SEPARATOR),
            404
        );

        return response()->file($absolutePath);
    }

    private function normalizePath(string $path): string
    {
        $normalizedPath = trim(str_replace('\\', '/', $path), '/');

        abort_if($normalizedPath === '' || str_contains($normalizedPath, "\0"), 404);

        $segments = explode('/', $normalizedPath);

        abort_if(
            in_array('', $segments, true)
                || in_array('.', $segments, true)
                || in_array('..', $segments, true),
            404
        );

        return $normalizedPath;
    }
}
