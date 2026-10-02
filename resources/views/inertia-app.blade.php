@php
    $siteSetting = \Illuminate\Support\Facades\Cache::rememberForever('site_setting', function () {
        return \App\Models\SiteSetting::first();
    });
    $appName = $siteSetting->nama_website ?? 'SILAB';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $appName }}</title>
    <meta name="application-name" content="{{ $appName }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @inertiaHead
    @vite(['resources/css/app.css', 'resources/js/inertia-app.js'])
</head>
<body class="antialiased bg-gray-50 text-gray-900">
    @inertia
</body>
</html>
