<?php

session_start();

require_once 'config.php';

spl_autoload_register(function ($class) {
    $caminhos = [
        dirname(__DIR__) . '/' . str_replace(['\\', 'App/'], ['/', 'app/'], $class) . '.php',
        dirname(__DIR__) . '/app/' . str_replace('\\', '/', $class) . '.php',
    ];

    foreach ($caminhos as $arquivo) {
        if (file_exists($arquivo)) {
            require_once $arquivo;
            return;
        }
    }
});
