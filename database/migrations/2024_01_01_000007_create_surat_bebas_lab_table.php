<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('surat_bebas_lab', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('tanggal_pengajuan');
            $table->enum('status', ['menunggu', 'masih_tanggungan', 'diverifikasi', 'disetujui'])->default('menunggu');
            $table->text('catatan')->nullable();
            $table->date('tanggal_disetujui')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('surat_bebas_lab'); }
};
