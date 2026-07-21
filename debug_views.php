<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';

try {
    $finder = $app->make('view.finder');
    echo "Looking for: admin.staffs.create\n";
    echo "View paths: " . implode(", ", $finder->getPaths()) . "\n";
    echo "File exists at expected path: " . (file_exists(base_path('resources/views/admin/staffs/create.blade.php')) ? 'YES' : 'NO') . "\n";
    
    $found = $finder->find('admin.staffs.create');
    echo "View finder result: " . $found . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
