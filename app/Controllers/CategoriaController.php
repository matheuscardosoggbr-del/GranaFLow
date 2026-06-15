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

    /**
     * Lista categorias do usuário
     */
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

    /**
     * Exibe formulário de edição de categoria
     */
    public function editar($id)
    {
        $id_usuario = $_SESSION['id_usuario'];
        $id = intval($id);
        
        $categoriaModel = $this->model('Categoria');
        $categoria = $categoriaModel->getCategoriaById($id);
        
        // Verificar se a categoria pertence ao usuário
        if (!$categoria || $categoria['id_usuario'] !== $id_usuario) {
            $_SESSION['erro'] = "Acesso negado.";
            redirecionar('categorias');
        }

        $data = [
            'categoria' => $categoria,
            'csrf_token' => $this->gerarTokenCSRF(),
        ];

        $this->view('categorias/form', $data);
    }

    /**
     * Adiciona nova categoria ou atualiza existente
     */
    public function salvar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirecionar('categorias');
        }

        $id_usuario = $_SESSION['id_usuario'];

        // Validar CSRF
        if (empty($_POST['csrf_token']) || !$this->validarTokenCSRF($_POST['csrf_token'])) {
            $_SESSION['erro'] = "Solicitação inválida.";
            redirecionar('categorias');
        }

        // Validar entrada
        $id_categoria = intval($_POST['id_categoria'] ?? 0);
        $nome = $this->sanitizar($_POST['nome'] ?? '');
        $id_tipo = intval($_POST['id_tipo'] ?? 2); // 2 = Despesa

        if (empty($nome) || strlen($nome) > 30) {
            $_SESSION['erro'] = "Nome de categoria inválido.";
            redirecionar($id_categoria > 0 ? "categorias/editar/$id_categoria" : 'categorias');
        }

        if ($id_tipo !== 1 && $id_tipo !== 2) {
            $_SESSION['erro'] = "Tipo de categoria inválido.";
            redirecionar($id_categoria > 0 ? "categorias/editar/$id_categoria" : 'categorias');
        }

        $categoriaModel = $this->model('Categoria');

        if ($id_categoria > 0) {
            // EDITAR categoria existente
            $categoria = $categoriaModel->getCategoriaById($id_categoria);
            
            // Verificar se a categoria pertence ao usuário
            if (!$categoria || $categoria['id_usuario'] !== $id_usuario) {
                $_SESSION['erro'] = "Acesso negado.";
                redirecionar('categorias');
            }

            // Não permitir editar categorias padrão (id_tipo = 0)
            if ($categoria['id_tipo'] === 0) {
                $_SESSION['erro'] = "Não é possível editar categorias padrão.";
                redirecionar('categorias');
            }

            if ($categoriaModel->atualizar($id_categoria, $nome, $id_tipo)) {
                $_SESSION['sucesso'] = "Categoria atualizada com sucesso!";
            } else {
                $_SESSION['erro'] = "Erro ao atualizar categoria.";
            }
        } else {
            // CRIAR nova categoria
            if ($categoriaModel->inserir($nome, $id_tipo, $id_usuario)) {
                $_SESSION['sucesso'] = "Categoria adicionada com sucesso!";
            } else {
                $_SESSION['erro'] = "Erro ao adicionar categoria.";
            }
        }

        redirecionar('categorias');
    }

    /**
     * Deleta uma categoria
     */
    public function deletar($id)
    {
        $id_usuario = $_SESSION['id_usuario'];
        $id = intval($id);

        if (empty($_POST['csrf_token']) || !$this->validarTokenCSRF($_POST['csrf_token'])) {
            $_SESSION['erro'] = "Solicitação inválida.";
            redirecionar('categorias');
        }

        $categoriaModel = $this->model('Categoria');

        // Verificar se a categoria pertence ao usuário
        $categoria = $categoriaModel->getCategoriaById($id);
        if (!$categoria || $categoria['id_usuario'] !== $id_usuario) {
            $_SESSION['erro'] = "Acesso negado.";
            redirecionar('categorias');
        }

        // Não permitir deletar categorias padrão
        if ($categoria['id_tipo'] === 0) {
            $_SESSION['erro'] = "Não é possível deletar categorias padrão.";
            redirecionar('categorias');
        }

        // Verificar se há gastos com essa categoria
        $gastoModel = $this->model('Gasto');
        $gastos = $gastoModel->getGastosByCategoria($id);
        if (count($gastos) > 0) {
            $_SESSION['erro'] = "Não é possível deletar uma categoria com gastos associados.";
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
