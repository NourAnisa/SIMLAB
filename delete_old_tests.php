<?php
// Script untuk menghapus file test per-role yang sudah digabung
// Jalankan: php delete_old_tests.php

$base = __DIR__ . '/tests/Feature/';

$toDelete = [
    // Stub files (empty stubs)
    'IzinPenelitianAdminTest.php',
    'IzinPenelitianKepalaLabTest.php',
    'IzinPenelitianMahasiswaTest.php',
    'PeminjamanLabAdminTest.php',
    'PeminjamanLabDosenTest.php',
    'PeminjamanLabKepalaLabTest.php',
    'PeminjamanLabMahasiswaTest.php',
    'SuratBebasLabAdminTest.php',
    'SuratBebasLabKepalaLabTest.php',
    'SuratBebasLabMahasiswaTest.php',
    'SertifikasiMahasiswaTest.php',
    'SertifikasiAdminTest.php',
    'SertifikasiKepalaLabTest.php',
    // Per-role files covered by merged feature tests
    'KepalaLabPeminjamanLabTest.php',
    'KepalaLabIzinPenelitianTest.php',
    'KepalaLabSertifikasiTest.php',
    'KepalaLabSuratBebasLabTest.php',
    'DosenModulTest.php',
    'DosenPeminjamanLabTest.php',
    // Generic profile test (replaced by ProfilFeatureTest.php)
    'ProfileTest.php',
];

foreach ($toDelete as $file) {
    $path = $base . $file;
    if (file_exists($path)) {
        unlink($path);
        echo "Deleted: $file\n";
    } else {
        echo "Not found: $file\n";
    }
}

echo "\nDone! Delete this script after running: delete delete_old_tests.php\n";
