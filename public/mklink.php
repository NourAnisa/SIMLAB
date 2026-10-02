<?php
$target = __DIR__ . '/../storage/app/public';
$link   = __DIR__ . '/storage';

if (is_link($link)) {
    echo '✅ Symlink sudah ada: ' . readlink($link);
} elseif (file_exists($link)) {
    echo '⚠️ Folder /public/storage sudah ada (bukan symlink). Hapus dulu via cPanel File Manager.';
} else {
    if (symlink($target, $link)) {
        echo '✅ Symlink berhasil dibuat!';
    } else {
        echo '❌ Gagal buat symlink. Coba cara manual.';
    }
}