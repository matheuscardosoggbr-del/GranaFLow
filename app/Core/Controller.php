<?php

namespace App\Core;

class Controller
{
    public function model($model)
    {
        $file = dirname(__DIR__) . '/Models/' . $model . '.php';
        if (!file_exists($file)) {
            throw new \RuntimeException("Model nÃ£o encontrado: {$model}");
        }
        require_once $file;
        $modelClass = 'App\\Models\\' . $model;
        return new $modelClass();
    }

    public function view($view, $data = [])
    {
        extract($data);
        $file = dirname(__DIR__) . '/Views/' . $view . '.php';
        if (!file_exists($file)) {
            throw new \RuntimeException("View nÃ£o encontrada: {$view}");
        }
        require_once $file;
    }
public function gerarTokenCSRF()
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
public function validarTokenCSRF($token)
    {
        return !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
public function sanitizar($dados)
    {
        if (is_array($dados)) {
            return array_map(function ($valor) {
                return $this->sanitizar($valor);
            }, $dados);
        }
        return htmlspecialchars(trim($dados), ENT_QUOTES, 'UTF-8');
    }
public function validarEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
public function validarValor($valor)
    {
        return is_numeric($valor) && $valor > 0;
    }
public function validarData($data, $formato = 'Y-m-d')
    {
        $date = \DateTime::createFromFormat($formato, $data);
        return $date && $date->format($formato) === $data;
    }
}

