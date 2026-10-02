<?php
namespace App;

use Illuminate\Http\Request;

class FileUploadService
{
    public function upload(Request $request, string $field, string $folder): ?string
    {
        if (!$request->hasFile($field)) return null;

        $request->validate([
            $field => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        return $request->file($field)->store($folder, 'public');
    }
}
