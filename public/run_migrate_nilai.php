<?php
// Run via browser: http://127.0.0.1:8000/run_migrate_nilai.php
chdir(dirname(__DIR__));
$output = shell_exec('php artisan migrate --force 2>&1');
echo '<pre>' . htmlspecialchars($output) . '</pre>';
unlink(__FILE__);
echo '<p>Script deleted.</p>';
