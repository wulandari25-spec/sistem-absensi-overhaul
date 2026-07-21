<?php
require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';

try {
    $view = $app['view']->make('admin.staffs.create');
    echo "View found!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    echo "\n\nLooking for: admin/staffs/create.blade.php";
    echo "\nPath: " . $app->basePath('resources/views/admin/staffs/create.blade.php');
    echo "\nExists: " . (file_exists($app->basePath('resources/views/admin/staffs/create.blade.php')) ? 'YES' : 'NO');
}
