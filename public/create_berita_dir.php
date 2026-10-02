<?php
// Helper: create missing view directories
$dirs = [
    'C:/xampp/htdocs/lab-management-system/resources/views/admin/berita',
];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
        echo "Created: $dir<br>";
    } else {
        echo "Already exists: $dir<br>";
    }
}
echo "<p style='color:green'>Done! <a href='/admin/berita'>Go to Berita →</a></p>";
@unlink(__FILE__);
