<?php
// Run once: adds contact/sosmed columns to site_settings table
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$cols = [
    'deskripsi'       => 'TEXT NULL AFTER `logo`',
    'alamat'          => 'VARCHAR(255) NULL AFTER `deskripsi`',
    'telepon'         => 'VARCHAR(50) NULL AFTER `alamat`',
    'email_kontak'    => 'VARCHAR(100) NULL AFTER `telepon`',
    'jam_operasional' => 'VARCHAR(100) NULL AFTER `email_kontak`',
    'facebook'        => 'VARCHAR(255) NULL AFTER `jam_operasional`',
    'instagram'       => 'VARCHAR(255) NULL AFTER `facebook`',
    'twitter'         => 'VARCHAR(255) NULL AFTER `instagram`',
    'youtube'         => 'VARCHAR(255) NULL AFTER `twitter`',
];

$added = [];
$skipped = [];

foreach ($cols as $col => $def) {
    if (!Schema::hasColumn('site_settings', $col)) {
        DB::statement("ALTER TABLE `site_settings` ADD COLUMN `$col` $def");
        $added[] = $col;
    } else {
        $skipped[] = $col;
    }
}

echo '<pre style="font-family:monospace;font-size:14px;padding:20px">';
echo "✅ Kolom ditambahkan: " . (count($added) ? implode(', ', $added) : 'tidak ada') . "\n";
echo "⏭ Kolom sudah ada: " . (count($skipped) ? implode(', ', $skipped) : 'tidak ada') . "\n";
echo "\n✅ Selesai! File ini otomatis dihapus.\n";
echo '</pre>';

unlink(__FILE__);
