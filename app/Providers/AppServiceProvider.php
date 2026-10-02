<?php

namespace App\Providers;

use App\Models\IzinPenelitian;
use App\Models\PeminjamanLab;
use App\Models\PendaftaranSertifikasi;
use App\Models\SuratBebasLab;
use App\Providers\ServiceObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        PendaftaranSertifikasi::observe(ServiceObserver::class);
        IzinPenelitian::observe(ServiceObserver::class);
        PeminjamanLab::observe(ServiceObserver::class);
        SuratBebasLab::observe(ServiceObserver::class);
    }
}
