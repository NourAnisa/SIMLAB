<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (!in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            return;
        }

        DB::statement("ALTER TABLE peminjaman_lab MODIFY COLUMN status ENUM('menunggu','diverifikasi','disetujui','ditolak','menunggu_kembali','dikembalikan') NOT NULL DEFAULT 'menunggu'");
    }

    public function down(): void
    {
        if (!in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            return;
        }

        DB::statement("ALTER TABLE peminjaman_lab MODIFY COLUMN status ENUM('menunggu','diverifikasi','disetujui','ditolak') NOT NULL DEFAULT 'menunggu'");
    }
};
