<?php

namespace App\Controllers;

use App\Core\Controller;

class CategoriaController extends Controller
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
        $categoriaModel = $this->model('Categoria');
        $categorias = $categoriaModel->getCategoriasPersonalizadas($id_usuario);

        $data = [
            'categorias' => $categorias,
            'csrf_token' => $this->gerarTokenCSRF(),
        ];

        $this->view('categorias/index', $data);
    }
public function editar($id)
    {
        $id_usuario = $_SESSION['id_usuario'];
        $id = intval($id);
        
        $categoriaModel = $this->model('Categoria');
        $categoria = $categoriaModel->getCategoriaById($id);
        if (!$categoria || (int)($categoria['id_usuario'] ?? 0) !== (int)$id_usuario) {
            $_SESSION['erro'] = "Acesso negado.";
            redirecionar('categorias');
        }

        $data = [
            'categoria' => $categoria,
            'csrf_token' => $this->gerarTokenCSRF(),
        ];

        $this->view('categorias/form', $data);
    }
public function salvar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirecionar('categorias');
        }

        $id_usuario = $_SESSION['id_usuario'];
        if (empty($_POST['csrf_token']) || !$this->validarTokenCSRF($_POST['csrf_token'])) {
            $_SESSION['erro'] = "SolicitaÃ§Ã£o invÃ¡lida.";
            redirecionar('categorias');
        }
        $id_categoria = intval($_POST['id_categoria'] ?? 0);
        $nome = $this->sanitizar($_POST['nome'] ?? '');
        $id_tipo = intval($_POST['id_tipo'] ?? 2);

        if (empty($nome) || strlen($nome) > 30) {
            $_SESSION['erro'] = "Nome de categoria invÃ¡lido.";
            redirecionar($id_categoria > 0 ? "categorias/editar/$id_categoria" : 'categorias');
        }

        if ($id_tipo !== 1 && $id_tipo !== 2) {
            $_SESSION['erro'] = "Tipo de categoria invÃ¡lido.";
            redirecionar($id_categoria > 0 ? "categorias/editar/$id_categoria" : 'categorias');
        }

        $categoriaModel = $this->model('Categoria');

        if ($id_categoria > 0) {
            $categoria = $categoriaModel->getCategoriaById($id_categoria);
            if (!$categoria || (int)($categoria['id_usuario'] ?? 0) !== (int)$id_usuario) {
                $_SESSION['erro'] = "Acesso negado.";
                redirecionar('categorias');
            }
            if (empty($categoria['id_usuario'])) {
                $_SESSION['erro'] = "NÃ£o Ã© possÃ­vel editar categorias padrÃ£o.";
                redirecionar('categorias');
            }

            if ($categoriaModel->atualizar($id_categoria, $nome, $id_tipo)) {
                $_SESSION['sucesso'] = "Categoria atualizada com sucesso!";
            } else {
                $_SESSION['erro'] = "Erro ao atualizar categoria.";
            }
        } else {
            if ($categoriaModel->inserir($nome, $id_tipo, $id_usuario)) {
                $_SESSION['sucesso'] = "Categoria adicionada com sucesso!";
            } else {
                $_SESSION['erro'] = "Erro ao adicionar categoria.";
            }
        }

        redirecionar('categorias');
    }
public function deletar($id)
    {
        $id_usuario = $_SESSION['id_usuario'];
        $id = intval($id);

        if (empty($_POST['csrf_token']) || !$this->validarTokenCSRF($_POST['csrf_token'])) {
            $_SESSION['erro'] = "SolicitaÃ§Ã£o invÃ¡lida.";
            redirecionar('categorias');
        }

        $categoriaModel = $this->model('Categoria');
        $categoria = $categoriaModel->getCategoriaById($id);
        if (!$categoria || (int)($categoria['id_usuario'] ?? 0) !== (int)$id_usuario) {
            $_SESSION['erro'] = "Acesso negado.";
            redirecionar('categorias');
        }
        if (empty($categoria['id_usuario'])) {
            $_SESSION['erro'] = "NÃ£o Ã© possÃ­vel deletar categorias padrÃ£o.";
            redirecionar('categorias');
        }
        $gastoModel = $this->model('Gasto');
        $gastos = $gastoModel->getGastosByCategoria($id);
        if (count($gastos) > 0) {
            $_SESSION['erro'] = "NÃ£o Ã© possÃ­vel deletar uma categoria com gastos associados.";
            redirecionar('categorias');
        }

        if ($categoriaModel->deletar($id)) {
            $_SESSION['sucesso'] = "Categoria deletada com sucesso!";
        } else {
            $_SESSION['erro'] = "Erro ao deletar categoria.";
        }

        redirecionar('categorias');
    }
}

