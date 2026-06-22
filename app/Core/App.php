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
        if (empty($url) || empty($url[0])) {
            $this->chamar();
            return;
        }
        $route = strtolower($url[0]);
        $aliases = [
            'categoria' => 'CategoriaController',
            'categorias' => 'CategoriaController',
            'perfil' => 'PerfilController',
            'relatorio' => 'RelatorioController',
            'relatorios' => 'RelatorioController',
            'receita' => 'ReceitasController',
            'receitas' => 'ReceitasController',
        ];
        $controllerNome = $aliases[$route] ?? (ucfirst($route) . 'Controller');
        $controllerArquivo = dirname(__DIR__) . '/Controllers/' . $controllerNome . '.php';

        if (!file_exists($controllerArquivo)) {
            $this->chamar();
            return;
        }

        $this->controller = $controllerNome;
        unset($url[0]);
        $url = array_values($url);

        $controllerClass = 'App\\Controllers\\' . $this->controller;
        $controllerObj   = new $controllerClass();
        if (!empty($url[0]) && method_exists($controllerObj, $url[0])) {
            $this->method = $url[0];
            unset($url[0]);
            $url = array_values($url);
        } else {
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

