<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Catatan: SIMLAB sekarang sepenuhnya menggunakan Inertia.js untuk routing dan request data.
// Endpoint API eksternal (jika nantinya ada untuk mobile app atau integrasi 3rd party) dapat ditempatkan di sini.

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
