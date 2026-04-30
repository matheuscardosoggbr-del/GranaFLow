<?php

namespace App\Controllers;

use App\Core\Controller;

class GastosController extends Controller
{
    public function __construct()
    {
        if (!isset($_SESSION['id_usuario'])) {
            redirecionar('auth/login');
        }
    }

    /**
     * Lista todos os gastos do usuário
     */
    public function index()
    {
        $id_usuario = $_SESSION['id_usuario'];
        $gastoModel = $this->model('Gasto');
        $categoriaModel = $this->model('Categoria');

        // Filtros
        $filtro_categoria = $_GET['categoria'] ?? null;
        $filtro_mes = $_GET['mes'] ?? date('Y-m');
        $ordem = $_GET['ordem'] ?? 'data_desc';

        $gastos = $gastoModel->getGastos($id_usuario, $filtro_categoria, $filtro_mes, $ordem);
        $categorias = $categoriaModel->getCategorias($id_usuario);

        $data = [
            'gastos' => $gastos,
            'categorias' => $categorias,
            'filtro_categoria' => $filtro_categoria,
            'filtro_mes' => $filtro_mes,
            'ordem' => $ordem,
            'total' => array_sum(array_column($gastos, 'valor')),
        ];

        $this->view('gastos/index', $data);
    }

    /**
     * Exibe formulário de novo gasto
     */
    public function novo()
    {
        $id_usuario = $_SESSION['id_usuario'];
        $categoriaModel = $this->model('Categoria');
        $categorias = $categoriaModel->getCategorias($id_usuario);

        $data = [
            'categorias' => $categorias,
            'csrf_token' => $this->gerarTokenCSRF(),
        ];

        $this->view('gastos/form', $data);
    }

    /**
     * Exibe formulário de edição
     */
    public function editar($id)
    {
        $id_usuario = $_SESSION['id_usuario'];
        $id = intval($id);
        
        $gastoModel = $this->model('Gasto');
        $categoriaModel = $this->model('Categoria');

        $gasto = $gastoModel->getGastoById($id, $id_usuario);
        if (!$gasto) {
            redirecionar('gastos');
        }

        $categorias = $categoriaModel->getCategorias($id_usuario);

        $data = [
            'gasto' => $gasto,
            'categorias' => $categorias,
            'csrf_token' => $this->gerarTokenCSRF(),
        ];

        $this->view('gastos/form', $data);
    }

    /**
     * Salva novo gasto ou edita existente
     */
    public function salvar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirecionar('gastos');
        }

        $id_usuario = $_SESSION['id_usuario'];

        // Validar CSRF
        if (empty($_POST['csrf_token']) || !$this->validarTokenCSRF($_POST['csrf_token'])) {
            $_SESSION['erro'] = "Solicitação inválida.";
            redirecionar('gastos');
        }

        // Validar entrada
        $id_gasto = intval($_POST['id_gasto'] ?? 0);
        $id_categoria = intval($_POST['id_categoria'] ?? 0);
        $descricao = $this->sanitizar($_POST['descricao'] ?? '');
        $valor = floatval(str_replace(',', '.', $_POST['valor'] ?? 0));
        $data_gasto = $this->sanitizar($_POST['data_gasto'] ?? '');

        if ($id_categoria <= 0) {
            $_SESSION['erro'] = "Categoria inválida.";
            redirecionar('gastos/novo');
        }

        if (empty($descricao) || strlen($descricao) > 255) {
            $_SESSION['erro'] = "Descrição inválida.";
            redirecionar('gastos/novo');
        }

        if (!$this->validarValor($valor)) {
            $_SESSION['erro'] = "Valor deve ser maior que zero.";
            redirecionar('gastos/novo');
        }

        if (!$this->validarData($data_gasto)) {
            $_SESSION['erro'] = "Data inválida.";
            redirecionar('gastos/novo');
        }

        $gastoModel = $this->model('Gasto');

        if ($id_gasto > 0) {
            // Editar
            if (!$gastoModel->pertenceAoUsuario('gastos', 'id_gasto', $id_gasto, $id_usuario)) {
                $_SESSION['erro'] = "Acesso negado.";
                redirecionar('gastos');
            }
            $sucesso = $gastoModel->atualizar($id_gasto, $id_categoria, $descricao, $valor, $data_gasto);
            $mensagem = "Gasto atualizado com sucesso!";
        } else {
            // Novo
            $sucesso = $gastoModel->adicionar($id_usuario, $id_categoria, $descricao, $valor, $data_gasto);
            $mensagem = "Gasto adicionado com sucesso!";
        }

        if ($sucesso) {
            $_SESSION['sucesso'] = $mensagem;
        } else {
            $_SESSION['erro'] = "Erro ao salvar gasto.";
        }

        redirecionar('gastos');
    }

    /**
     * Verifica se é requisição AJAX
     */
    private function isAjax()
    {
        return isset($_POST['_ajax']) || isset($_GET['_ajax']) || 
               (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
    }

    /**
     * Retorna resposta JSON
     */
    private function jsonResponse($success, $message = '', $data = [])
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data' => $data,
        ]);
        exit;
    }

    /**
     * Adiciona gasto (com suporte AJAX)
     */
    public function adicionar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirecionar('gastos');
        }

        $id_usuario = $_SESSION['id_usuario'];
        $id_gasto_edit = intval($_POST['id_gasto'] ?? 0); // para edição via modal
        $id_categoria = intval($_POST['id_categoria'] ?? 0);
        $descricao = $this->sanitizar($_POST['descricao'] ?? '');
        $valor = floatval(str_replace(',', '.', $_POST['valor'] ?? 0));
        $data_gasto = $this->sanitizar($_POST['data_gasto'] ?? date('Y-m-d'));

        // Validações
        if ($id_categoria <= 0) {
            if ($this->isAjax()) {
                $this->jsonResponse(false, 'Categoria inválida.');
            }
            $_SESSION['erro'] = "Categoria inválida.";
            redirecionar('gastos');
        }

        if (empty($descricao) || strlen($descricao) > 255) {
            if ($this->isAjax()) {
                $this->jsonResponse(false, 'Descrição inválida (máx 255 caracteres).');
            }
            $_SESSION['erro'] = "Descrição inválida.";
            redirecionar('gastos/novo');
        }

        if ($valor <= 0) {
            if ($this->isAjax()) {
                $this->jsonResponse(false, 'Valor deve ser maior que zero.');
            }
            $_SESSION['erro'] = "Valor deve ser maior que zero.";
            redirecionar('gastos/novo');
        }

        if (!$this->validarData($data_gasto)) {
            if ($this->isAjax()) {
                $this->jsonResponse(false, 'Data inválida.');
            }
            $_SESSION['erro'] = "Data inválida.";
            redirecionar('gastos/novo');
        }

        $gastoModel = $this->model('Gasto');

        // Se id_gasto_edit > 0, é edição (via modal no dashboard)
        if ($id_gasto_edit > 0) {
            if (!$gastoModel->pertenceAoUsuario('gastos', 'id_gasto', $id_gasto_edit, $id_usuario)) {
                if ($this->isAjax()) { $this->jsonResponse(false, 'Acesso negado.'); }
                redirecionar('gastos');
            }
            $sucesso = $gastoModel->atualizar($id_gasto_edit, $id_categoria, $descricao, $valor, $data_gasto);
            if ($this->isAjax()) {
                $this->jsonResponse(true, 'Gasto atualizado com sucesso!', 
                    $this->getDashData($id_usuario));
            }
            $_SESSION['sucesso'] = "Gasto atualizado com sucesso!";
            redirecionar('gastos');
        }

        if ($gastoModel->adicionar($id_usuario, $id_categoria, $descricao, $valor, $data_gasto)) {
            if ($this->isAjax()) {
                $this->jsonResponse(true, 'Gasto adicionado com sucesso!', 
                    $this->getDashData($id_usuario));
            }
            $_SESSION['sucesso'] = "Gasto adicionado com sucesso!";
        } else {
            if ($this->isAjax()) {
                $this->jsonResponse(false, 'Erro ao adicionar gasto.');
            }
            $_SESSION['erro'] = "Erro ao adicionar gasto.";
        }

        redirecionar('gastos');
    }

    /**
     * Deleta um gasto
     * 🔧 CORREÇÃO: Agora retorna dados atualizados do dashboard
     */
    public function deletar($id = null)
    {
        $id_usuario = $_SESSION['id_usuario'];
        $id = intval($id ?? ($_GET['id'] ?? 0));

        if ($id <= 0) {
            if ($this->isAjax()) {
                $this->jsonResponse(false, 'ID inválido.');
            }
            $_SESSION['erro'] = "ID inválido.";
            redirecionar('gastos');
        }

        $gastoModel = $this->model('Gasto');
        
        if (!$gastoModel->pertenceAoUsuario('gastos', 'id_gasto', $id, $id_usuario)) {
            if ($this->isAjax()) {
                $this->jsonResponse(false, 'Acesso negado.');
            }
            $_SESSION['erro'] = "Acesso negado.";
            redirecionar('gastos');
        }

        if ($gastoModel->deletar($id, $id_usuario)) {
            if ($this->isAjax()) {
                // ✅ CORREÇÃO: Agora retorna dados atualizados
                $this->jsonResponse(true, 'Gasto deletado com sucesso!', 
                    $this->getDashData($id_usuario));
            }
            $_SESSION['sucesso'] = "Gasto deletado com sucesso!";
        } else {
            if ($this->isAjax()) {
                $this->jsonResponse(false, 'Erro ao deletar gasto.');
            }
            $_SESSION['erro'] = "Erro ao deletar gasto.";
        }

        redirecionar('gastos');
    }

    /**
     * ✅ NOVO MÉTODO: Retorna dados atualizados do dashboard para AJAX
     * Evita duplicação de código entre adicionar, editar e deletar
     */
    private function getDashData($id_usuario)
    {
        $gastoModel    = $this->model('Gasto');
        $metaModel     = $this->model('Meta');
        $salarioModel  = $this->model('Salario');
        $poupancaModel = $this->model('Poupanca');

        // Buscar dados do banco com fallback para valores vazios
        $gastos         = $gastoModel->getGastos($id_usuario) ?? [];
        $metas          = $metaModel->getMetas($id_usuario) ?? [];
        $salario        = $salarioModel->getSalario($id_usuario) ?? 0;
        $recorrentes    = $gastoModel->getRecorrentes($id_usuario) ?? [];
        $total_guardado = $poupancaModel->getTotalGuardado($id_usuario) ?? 0;
        $historico_guardado = $poupancaModel->getHistorico($id_usuario, 5) ?? [];

        // Calcular totais do mês
        $mes = date('m');
        $ano = date('Y');
        $gm = array_filter($gastos, fn($g) => 
            date('m', strtotime($g['data_gasto'] ?? date('Y-m-d'))) == $mes && 
            date('Y', strtotime($g['data_gasto'] ?? date('Y-m-d'))) == $ano
        );
        $total_mes = array_sum(array_column($gm, 'valor')) ?? 0;
        
        // Calcular saldo
        $saldo = floatval($salario - $total_mes - $total_guardado);

        return [
            'gastos'             => $gastos,
            'recorrentes'        => $recorrentes,
            'metas'              => $metas,
            'salario'            => floatval($salario),
            'total_mes'          => floatval($total_mes),
            'total_geral'        => floatval(array_sum(array_column($gastos, 'valor')) ?? 0),
            'total_guardado'     => floatval($total_guardado),
            'saldo'              => $saldo,
            'historico_guardado' => $historico_guardado,
        ];
    }
}
;