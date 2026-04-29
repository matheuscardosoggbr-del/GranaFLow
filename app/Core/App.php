<?php

namespace App\Core;

class App
{
    protected $controller = 'AuthController';
    protected $method     = 'login';
    protected $params     = [];

    public function __construct()
    {
        $url = $this->parseUrl();

        // Se URL vazia → vai direto para AuthController->login
        if (empty($url) || empty($url[0])) {
            $this->chamar();
            return;
        }

        // Define o controller a partir do primeiro segmento da URL
        $controllerNome    = ucfirst(strtolower($url[0])) . 'Controller';
        $controllerArquivo = dirname(__DIR__) . '/Controllers/' . $controllerNome . '.php';

        if (!file_exists($controllerArquivo)) {
            // Controller não encontrado → fallback para login
            $this->chamar();
            return;
        }

        $this->controller = $controllerNome;
        unset($url[0]);
        $url = array_values($url);

        $controllerClass = 'App\\Controllers\\' . $this->controller;
        $controllerObj   = new $controllerClass();

        // Define o método a partir do segundo segmento
        if (!empty($url[0]) && method_exists($controllerObj, $url[0])) {
            $this->method = $url[0];
            unset($url[0]);
            $url = array_values($url);
        } else {
            // Método não informado → usa o padrão do controller
            // Para Auth o padrão é login, para Dashboard é index, etc.
            $defaults = [
                'AuthController'      => 'login',
                'DashboardController' => 'index',
                'GastosController'    => 'index',
                'MetasController'     => 'index',
            ];
            $this->method = $defaults[$this->controller] ?? 'index';
        }

        $this->params = $url;

        call_user_func_array([$controllerObj, $this->method], $this->params);
    }

    protected function parseUrl()
    {
        if (isset($_GET['url'])) {
            return array_values(explode(
                '/',
                filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL)
            ));
        }
        return [];
    }

    protected function chamar()
    {
        $controllerClass = 'App\\Controllers\\' . $this->controller;
        $obj = new $controllerClass();
        call_user_func_array([$obj, $this->method], $this->params);
    }
}
