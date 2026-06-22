<?php

function env($chave, $padrao = null) {
    $valor = $_ENV[$chave] ?? $_SERVER[$chave] ?? getenv($chave);
    return $valor === false || $valor === null || $valor === '' ? $padrao : $valor;
}

$appUrl = env('APP_URL');

if ($appUrl) {
    $base_url = rtrim($appUrl, '/') . '/';
} else {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $public_path = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    $base_url = $protocol . $host . rtrim($public_path, '/') . '/';
}

define('BASE_URL', $base_url);
define('APP_PATH', dirname(__DIR__));
define('PUBLIC_PATH', dirname(__DIR__) . '/public/');

function redirecionar($rota) {
    $rota = ltrim($rota, '/');
    header('Location: ' . BASE_URL . $rota);
    exit();
}

