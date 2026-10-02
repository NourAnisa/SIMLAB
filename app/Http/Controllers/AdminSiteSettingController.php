<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class AdminSiteSettingController extends Controller
{
    public function index(Request $request)
    {
        $setting = SiteSetting::getSetting();
        $mailUsername = env('MAIL_USERNAME', '');

        return Inertia::render('Admin/SiteSetting/Index', [
            'setting' => $setting,
            'mail_username' => $mailUsername
        ]);
    }

    private function setEnv($key, $value)
    {
        $path = base_path('.env');
        if (file_exists($path)) {
            $content = file_get_contents($path);
            if (preg_match("/^{$key}=.*/m", $content)) {
                $content = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $content);
            } else {
                $content .= "\n{$key}={$value}";
            }
            file_put_contents($path, $content);
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'nama_website'    => 'required|max:100',
            'nama_kampus'     => 'required|max:150',
            'logo'            => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
            'deskripsi'       => 'nullable|max:500',
            'alamat'          => 'nullable|max:255',
            'telepon'         => 'nullable|max:50',
            'email_kontak'    => 'nullable|email|max:100',
            'jam_operasional' => 'nullable|max:100',
            'facebook'        => 'nullable|url|max:255',
            'instagram'       => 'nullable|url|max:255',
            'twitter'         => 'nullable|url|max:255',
            'youtube'         => 'nullable|url|max:255',
            'gallery.*'       => 'nullable|image|mimes:png,jpg,jpeg,webp|max:3072',
            'delete_gallery'  => 'nullable|array',
            'daftar_ruangan'  => 'nullable|string',
            'tema_warna'      => 'nullable|in:ungu,merah',
            'domain_email_mahasiswa' => 'nullable|string|max:100',
            'domain_email_pegawai' => 'nullable|string|max:100',
            'mail_username'   => 'nullable|string',
            'mail_password'   => 'nullable|string',
        ]);

        $setting = SiteSetting::firstOrNew(['id' => 1]);
        $setting->nama_website    = $request->nama_website;
        $setting->nama_kampus     = $request->nama_kampus;
        $setting->deskripsi       = $request->deskripsi;
        $setting->alamat          = $request->alamat;
        $setting->telepon         = $request->telepon;
        $setting->email_kontak    = $request->email_kontak;
        $setting->jam_operasional = $request->jam_operasional;
        $setting->facebook        = $request->facebook;
        $setting->instagram       = $request->instagram;
        $setting->twitter         = $request->twitter;
        $setting->youtube         = $request->youtube;
        $setting->domain_email_mahasiswa = $request->domain_email_mahasiswa;
        $setting->domain_email_pegawai = $request->domain_email_pegawai;
        
        if ($request->has('daftar_ruangan')) {
            $setting->daftar_ruangan  = $request->daftar_ruangan;
        }
        if ($request->has('tema_warna')) {
            $setting->tema_warna = $request->tema_warna;
        }

        if ($request->hasFile('logo')) {
            if ($setting->logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($setting->logo)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($setting->logo);
            }
            $setting->logo = $request->file('logo')->store('site', 'public');
        }

        // Handle gallery: remove deleted ones, add new ones
        $gallery = $setting->gallery_images ?? [];
        if ($request->filled('delete_gallery')) {
            foreach ($request->delete_gallery as $path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
                $gallery = array_values(array_filter($gallery, fn($g) => $g !== $path));
            }
        }
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {
                $gallery[] = $file->store('site/gallery', 'public');
            }
        }
        $setting->gallery_images = $gallery ?: null;

        $setting->save();

        \Illuminate\Support\Facades\Cache::forget('site_setting');

        if ($request->filled('mail_username')) {
            $this->setEnv('MAIL_USERNAME', $request->mail_username);
        }
        if ($request->filled('mail_password')) {
            $this->setEnv('MAIL_PASSWORD', $request->mail_password);
        }
        if ($request->filled('mail_username') || $request->filled('mail_password')) {
            \Illuminate\Support\Facades\Artisan::call('config:clear');
        }

        return back()->with('success', 'Profil website berhasil disimpan.');
    }
}
