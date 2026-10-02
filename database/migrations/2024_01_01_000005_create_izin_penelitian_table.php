<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('izin_penelitian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('judul_penelitian', 255);
            $table->text('tujuan');
            $table->string('dokumen_berkas')->nullable();
            $table->enum('status', ['menunggu', 'diverifikasi', 'disetujui', 'ditolak'])->default('menunggu');
            $table->text('catatan_verifikasi')->nullable();
            $table->date('tanggal_disetujui')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('izin_penelitian'); }
};
