<?php

session_start();

// config.php está na mesma pasta (app/)
require_once __DIR__ . '/config.php';

spl_autoload_register(function ($class) {
    // Ex: App\Controllers\AuthController → app/Controllers/AuthController.php
    if (strpos($class, 'App\\') === 0) {
        $relative = substr($class, 4); // Remove "App\"
        $relative = str_replace('\\', '/', $relative); // Troca \ por /
        $file = dirname(__DIR__) . '/app/' . $relative . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }
});
