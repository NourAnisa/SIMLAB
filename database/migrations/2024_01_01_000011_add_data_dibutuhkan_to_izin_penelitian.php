<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('izin_penelitian', function (Blueprint $table) {
            $table->json('data_dibutuhkan')->nullable()->after('dokumen_berkas');
        });
    }
    public function down(): void
    {
        Schema::table('izin_penelitian', function (Blueprint $table) {
            $table->dropColumn('data_dibutuhkan');
        });
    }
};
