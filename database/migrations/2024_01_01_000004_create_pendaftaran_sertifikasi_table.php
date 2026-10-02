<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pendaftaran_sertifikasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('jadwal_id')->constrained('jadwal_sertifikasi')->cascadeOnDelete();
            $table->string('dokumen_berkas')->nullable();
            $table->enum('status', ['menunggu', 'diverifikasi', 'lulus', 'tidak_lulus'])->default('menunggu');
            $table->string('token_ujian', 10)->nullable();
            $table->text('jawaban_ujian')->nullable();
            $table->text('hasil_validasi')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('pendaftaran_sertifikasi'); }
};
