<?php
// Creates kategori column in beritas table
$host = '127.0.0.1';
$db = 'lab_management';
$user = 'root';
$pass = '';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Add kategori column if not exists
    $cols = $pdo->query("SHOW COLUMNS FROM `beritas` LIKE 'kategori'")->fetchAll();
    if (empty($cols)) {
        $pdo->exec("ALTER TABLE `beritas` ADD COLUMN `kategori` VARCHAR(50) NULL AFTER `judul`");
        echo "<p style='color:green'>✓ Kolom kategori berhasil ditambahkan.</p>";
    } else {
        echo "<p style='color:blue'>ℹ Kolom kategori sudah ada.</p>";
    }

    // Add ringkasan column if not exists
    $cols2 = $pdo->query("SHOW COLUMNS FROM `beritas` LIKE 'ringkasan'")->fetchAll();
    if (empty($cols2)) {
        $pdo->exec("ALTER TABLE `beritas` ADD COLUMN `ringkasan` VARCHAR(255) NULL AFTER `kategori`");
        echo "<p style='color:green'>✓ Kolom ringkasan berhasil ditambahkan.</p>";
    } else {
        echo "<p style='color:blue'>ℹ Kolom ringkasan sudah ada.</p>";
    }

    echo "<h2 style='color:green'>✓ Selesai!</h2>";
    echo "<p><a href='/admin/berita'>Ke Manajemen Berita →</a></p>";
} catch (Exception $e) {
    echo "<h2 style='color:red'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</h2>";
}
@unlink(__FILE__);
