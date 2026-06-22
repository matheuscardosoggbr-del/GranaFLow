<?php

namespace App\Controllers;

use App\Core\Controller;

class SalarioController extends Controller
{
    public function __construct()
    {
        if (!isset($_SESSION['id_usuario'])) {
            redirecionar('auth/login');
        }
    }
public function index()
    {
        $id_usuario = $_SESSION['id_usuario'];
        $salarioModel = $this->model('Salario');
        $salario = $salarioModel->getSalario($id_usuario);
        $historico = $salarioModel->getHistorico($id_usuario, 6);

        $data = [
            'salario' => $salario,
            'historico' => $historico,
            'csrf_token' => $this->gerarTokenCSRF(),
        ];

        $this->view('salario/index', $data);
    }
public function salvar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirecionar('salario');
        }

        $id_usuario = $_SESSION['id_usuario'];
        if (empty($_POST['csrf_token']) || !$this->validarTokenCSRF($_POST['csrf_token'])) {
            $_SESSION['erro'] = "SolicitaÃ§Ã£o invÃ¡lida.";
            redirecionar('salario');
        }

        $valor = floatval(str_replace(',', '.', $_POST['valor'] ?? 0));

        if (!$this->validarValor($valor)) {
            $_SESSION['erro'] = "Valor do salÃ¡rio deve ser maior que zero.";
            redirecionar('salario');
        }

        $salarioModel = $this->model('Salario');
        if ($salarioModel->setSalario($id_usuario, $valor)) {
            $_SESSION['sucesso'] = "SalÃ¡rio atualizado com sucesso!";
        } else {
            $_SESSION['erro'] = "Erro ao atualizar salÃ¡rio.";
        }

        redirecionar('salario');
    }
}

