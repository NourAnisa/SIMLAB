<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('site_settings')) {
            Schema::create('site_settings', function (Blueprint $table) {
                $table->id();
                $table->string('nama_website')->nullable();
                $table->string('nama_kampus')->nullable();
                $table->string('logo')->nullable();
                $table->text('deskripsi')->nullable();
                $table->text('alamat')->nullable();
                $table->string('telepon')->nullable();
                $table->string('email_kontak')->nullable();
                $table->string('jam_operasional')->nullable();
                $table->string('facebook')->nullable();
                $table->string('instagram')->nullable();
                $table->string('twitter')->nullable();
                $table->string('youtube')->nullable();
                $table->json('gallery_images')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void {
        Schema::dropIfExists('site_settings');
    }
};
