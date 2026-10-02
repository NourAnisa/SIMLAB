<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('site_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('site_settings', 'deskripsi')) {
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
            }
        });
    }

    public function down(): void {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'deskripsi', 'alamat', 'telepon', 'email_kontak', 
                'jam_operasional', 'facebook', 'instagram', 'twitter', 
                'youtube', 'gallery_images'
            ]);
        });
    }
};
