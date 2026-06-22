<?php

namespace App\Controllers;

use App\Core\Controller;

class ReceitasController extends Controller
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
        $receitaModel = $this->model('Receita');

        $filtro_mes = $_GET['mes'] ?? date('Y-m');
        $ordem = $_GET['ordem'] ?? 'data_desc';
        $busca = trim($_GET['q'] ?? '');

        $receitas = $receitaModel->getReceitas($id_usuario, $filtro_mes, $ordem, $busca);

        $data = [
            'receitas' => $receitas,
            'filtro_mes' => $filtro_mes,
            'ordem' => $ordem,
            'busca' => $busca,
            'total' => array_sum(array_column($receitas, 'valor')),
            'csrf_token' => $this->gerarTokenCSRF(),
        ];

        $this->view('receitas/index', $data);
    }

    public function novo()
    {
        $this->view('receitas/form', [
            'csrf_token' => $this->gerarTokenCSRF(),
        ]);
    }

    public function editar($id)
    {
        $id_usuario = $_SESSION['id_usuario'];
        $id = intval($id);

        $receitaModel = $this->model('Receita');
        $receita = $receitaModel->getReceitaById($id, $id_usuario);

        if (!$receita) {
            redirecionar('receitas');
        }

        $this->view('receitas/form', [
            'receita' => $receita,
            'csrf_token' => $this->gerarTokenCSRF(),
        ]);
    }

    public function salvar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirecionar('receitas');
        }

        if (empty($_POST['csrf_token']) || !$this->validarTokenCSRF($_POST['csrf_token'])) {
            $_SESSION['erro'] = 'SolicitaÃ§Ã£o invÃ¡lida.';
            redirecionar('receitas');
        }

        $id_usuario = $_SESSION['id_usuario'];
        $id_receita = intval($_POST['id_receita'] ?? 0);
        $descricao = $this->sanitizar($_POST['descricao'] ?? '');
        $valor = floatval(str_replace(',', '.', $_POST['valor'] ?? 0));
        $data_receita = $this->sanitizar($_POST['data_receita'] ?? '');

        if (empty($descricao) || strlen($descricao) > 255) {
            $_SESSION['erro'] = 'DescriÃ§Ã£o invÃ¡lida.';
            redirecionar($id_receita > 0 ? "receitas/editar/$id_receita" : 'receitas/novo');
        }

        if (!$this->validarValor($valor)) {
            $_SESSION['erro'] = 'Valor deve ser maior que zero.';
            redirecionar($id_receita > 0 ? "receitas/editar/$id_receita" : 'receitas/novo');
        }

        if (!$this->validarData($data_receita)) {
            $_SESSION['erro'] = 'Data invÃ¡lida.';
            redirecionar($id_receita > 0 ? "receitas/editar/$id_receita" : 'receitas/novo');
        }

        $receitaModel = $this->model('Receita');

        if ($id_receita > 0) {
            $receita = $receitaModel->getReceitaById($id_receita, $id_usuario);
            if (!$receita) {
                $_SESSION['erro'] = 'Acesso negado.';
                redirecionar('receitas');
            }

            $sucesso = $receitaModel->atualizar($id_receita, $descricao, $valor, $data_receita);
            $mensagem = 'Receita atualizada com sucesso!';
        } else {
            $sucesso = $receitaModel->adicionar($id_usuario, $descricao, $valor, $data_receita);
            $mensagem = 'Receita adicionada com sucesso!';
        }

        $_SESSION[$sucesso ? 'sucesso' : 'erro'] = $sucesso ? $mensagem : 'Erro ao salvar receita.';
        redirecionar('receitas');
    }

    public function deletar($id = null)
    {
        $id_usuario = $_SESSION['id_usuario'];
        $id = intval($id ?? ($_GET['id'] ?? 0));

        if (empty($_POST['csrf_token']) || !$this->validarTokenCSRF($_POST['csrf_token'])) {
            $_SESSION['erro'] = 'SolicitaÃ§Ã£o invÃ¡lida.';
            redirecionar('receitas');
        }

        if ($id <= 0) {
            $_SESSION['erro'] = 'ID invÃ¡lido.';
            redirecionar('receitas');
        }

        $receitaModel = $this->model('Receita');
        if (!$receitaModel->deletar($id, $id_usuario)) {
            $_SESSION['erro'] = 'Erro ao deletar receita.';
        } else {
            $_SESSION['sucesso'] = 'Receita deletada com sucesso!';
        }

        redirecionar('receitas');
    }
}

