<?php
$files = [
    __DIR__ . '/run_migrate_nilai.php',
    __DIR__ . '/run-migrate.php',
    __DIR__ . '/make_dirs.php',
    __DIR__ . '/create_berita_dir.php',
    __DIR__ . '/add_site_setting_cols.php',
    __DIR__ . '/add_berita_cols.php',
];
foreach ($files as $f) {
    if (file_exists($f)) { unlink($f); echo "Deleted: " . basename($f) . "\n"; }
    else { echo "Not found: " . basename($f) . "\n"; }
}
unlink(__FILE__);
echo "Done!";
