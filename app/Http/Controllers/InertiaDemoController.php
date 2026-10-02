<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class InertiaDemoController extends Controller
{
    /**
     * Halaman demonstrasi Inertia.js.
     * 
     * Data (props) dikirim langsung ke komponen Vue melalui Inertia::render().
     * Tidak menggunakan Axios — ini yang membedakan Inertia dari SPA biasa.
     * 
     * Route: GET /inertia-demo
     * View:  resources/js/Pages/InertiaDemo.vue
     * Blade: resources/views/inertia-app.blade.php
     */
    public function index(): Response
    {
        return Inertia::render('InertiaDemo', [
            'appName'        => config('app.name', 'SILAB'),
            'laravelVersion' => app()->version(),
            'phpVersion'     => PHP_VERSION,
            'message'        => 'Data ini dikirim dari Laravel Controller — tanpa Axios, tanpa fetch!',
        ]);
    }
}
