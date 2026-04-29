<?php

// Carrega o inicializador (session_start + autoload + config)
require_once dirname(__DIR__) . '/app/init.php';

// Inicia o roteador
require_once dirname(__DIR__) . '/app/Core/App.php';

new \App\Core\App();
