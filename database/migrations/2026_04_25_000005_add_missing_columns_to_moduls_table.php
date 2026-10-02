<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('moduls', function (Blueprint $table) {
            if (!Schema::hasColumn('moduls', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade')->after('id');
            }
            if (!Schema::hasColumn('moduls', 'mata_kuliah')) {
                $table->string('mata_kuliah')->nullable()->after('nama_modul');
            }
            if (!Schema::hasColumn('moduls', 'deskripsi')) {
                $table->text('deskripsi')->nullable()->after('mata_kuliah');
            }
        });
    }

    public function down(): void {
        Schema::table('moduls', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'mata_kuliah', 'deskripsi']);
        });
    }
};
