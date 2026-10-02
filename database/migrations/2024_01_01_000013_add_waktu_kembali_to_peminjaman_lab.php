<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('peminjaman_lab', function (Blueprint $table) {
            $table->timestamp('waktu_dikembalikan')->nullable();
            $table->timestamp('waktu_diterima_admin')->nullable();
        });
    }

    public function down(): void {
        Schema::table('peminjaman_lab', function (Blueprint $table) {
            $table->dropColumn(['waktu_dikembalikan', 'waktu_diterima_admin']);
        });
    }
};
