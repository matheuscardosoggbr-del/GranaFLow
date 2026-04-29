<?php

// Detecção dinâmica da URL Base
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];

// SCRIPT_NAME é algo como /GITTRABALHOS/project/project_fixed/public/index.php
// dirname() disso = /GITTRABALHOS/project/project_fixed/public
// Queremos manter o /public/ no BASE_URL para os assets, mas sem duplicar nas rotas
$public_path = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$base_url = $protocol . $host . rtrim($public_path, '/') . '/';

define('BASE_URL', $base_url);

// Caminhos do sistema
define('APP_PATH', dirname(__DIR__));
define('PUBLIC_PATH', dirname(__DIR__) . '/public/');

/**
 * Função auxiliar para redirecionamentos
 */
function redirecionar($rota) {
    $rota = ltrim($rota, '/');
    header("Location: " . BASE_URL . $rota);
    exit();
}
