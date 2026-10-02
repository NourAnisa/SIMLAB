<?php
$output = shell_exec('cd /d C:\\xampp\\htdocs\\lab-management-system && php artisan migrate --force 2>&1');
echo nl2br(htmlspecialchars($output));
