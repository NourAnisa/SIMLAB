<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('jadwal_sertifikasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skema_id')->constrained('skema_sertifikasi')->cascadeOnDelete();
            $table->date('tanggal');
            $table->integer('kuota');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('jadwal_sertifikasi'); }
};
