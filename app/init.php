<?php

session_start();

function carregarEnv($caminho)
{
    if (!is_file($caminho)) {
        return;
    }

    $linhas = file($caminho, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($linhas === false) {
        return;
    }

    foreach ($linhas as $linha) {
        $linha = trim($linha);
        if ($linha === '' || str_starts_with($linha, '#') || !str_contains($linha, '=')) {
            continue;
        }

        [$chave, $valor] = explode('=', $linha, 2);
        $chave = trim($chave);
        $valor = trim($valor);

        if ($valor !== '' && (
            ($valor[0] === '"' && substr($valor, -1) === '"') ||
            ($valor[0] === "'" && substr($valor, -1) === "'")
        )) {
            $valor = substr($valor, 1, -1);
        }

        $_ENV[$chave] = $valor;
        $_SERVER[$chave] = $valor;
        putenv($chave . '=' . $valor);
    }
}

carregarEnv(dirname(__DIR__) . '/.env');

require_once __DIR__ . '/config.php';

spl_autoload_register(function ($class) {
    if (strpos($class, 'App\\') === 0) {
        $relative = substr($class, 4);
        $relative = str_replace('\\', '/', $relative);
        $file = dirname(__DIR__) . '/app/' . $relative . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }
});

