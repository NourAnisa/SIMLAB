<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('beritas', function (Blueprint $table) {
            if (!Schema::hasColumn('beritas', 'kategori')) {
                $table->string('kategori')->nullable()->after('judul');
            }
            if (!Schema::hasColumn('beritas', 'ringkasan')) {
                $table->string('ringkasan')->nullable()->after('kategori');
            }
        });
    }

    public function down(): void {
        Schema::table('beritas', function (Blueprint $table) {
            $table->dropColumn(['kategori', 'ringkasan']);
        });
    }
};
