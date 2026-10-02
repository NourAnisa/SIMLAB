<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

Schema::table('site_settings', function (Blueprint $table) {
    if (!Schema::hasColumn('site_settings', 'domain_email_mahasiswa')) {
        $table->string('domain_email_mahasiswa')->nullable();
    }
    if (!Schema::hasColumn('site_settings', 'domain_email_pegawai')) {
        $table->string('domain_email_pegawai')->nullable();
    }
});
echo "Done\n";
