<?php

if (php_sapi_name() === 'cli-server') {
    // Serve static files directly when using the PHP built-in server
    $url = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

// Load the Symfony app
require __DIR__ . '/app_dev.php';