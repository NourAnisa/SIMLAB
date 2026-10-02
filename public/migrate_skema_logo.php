<?php
// Run once at: http://127.0.0.1:8000/migrate_skema_logo.php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=lab_management', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $cols = $pdo->query("SHOW COLUMNS FROM skema_sertifikasi")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('logo', $cols)) {
        $pdo->exec("ALTER TABLE skema_sertifikasi ADD COLUMN `logo` VARCHAR(255) NULL AFTER `deskripsi`");
        echo "✅ Column 'logo' added to skema_sertifikasi.\n";
    } else {
        echo "ℹ️ Column 'logo' already exists.\n";
    }
    echo "Done!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
