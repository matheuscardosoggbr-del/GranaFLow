<?php

namespace App\Controllers;

use App\Core\Controller;

class MetasController extends Controller
{
    public function __construct()
    {
        if (!isset($_SESSION['id_usuario'])) {
            redirecionar('auth/login');
        }
    }

    /**
     * Lista todas as metas do usuário
     */
    public function index()
    {
        $id_usuario = $_SESSION['id_usuario'];
        $metaModel = $this->model('Meta');
        $metas = $metaModel->getMetas($id_usuario);

        $data = [
            'metas' => $metas,
            'csrf_token' => $this->gerarTokenCSRF(),
        ];

        $this->view('metas/index', $data);
    }

    /**
     * Exibe formulário de nova meta
     */
    public function novo()
    {
        $data = [
            'csrf_token' => $this->gerarTokenCSRF(),
        ];

        $this->view('metas/form', $data);
    }

    /**
     * Exibe formulário de edição
     */
    public function editar($id)
    {
        $id_usuario = $_SESSION['id_usuario'];
        $id = intval($id);
        
        $metaModel = $this->model('Meta');
        $meta = $metaModel->getMetaById($id, $id_usuario);
        
        if (!$meta) {
            redirecionar('metas');
        }

        $data = [
            'meta' => $meta,
            'csrf_token' => $this->gerarTokenCSRF(),
        ];

        $this->view('metas/form', $data);
    }

    /**
     * Salva nova meta ou edita existente
     */
    public function salvar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirecionar('metas');
        }

        $id_usuario = $_SESSION['id_usuario'];

        // Validar CSRF
        if (empty($_POST['csrf_token']) || !$this->validarTokenCSRF($_POST['csrf_token'])) {
            $_SESSION['erro'] = "Solicitação inválida.";
            redirecionar('metas');
        }

        // Validar entrada
        $id_meta = intval($_POST['id_meta'] ?? 0);
        $nome = $this->sanitizar($_POST['nome_meta'] ?? '');
        $valor = floatval(str_replace(',', '.', $_POST['valor_limite'] ?? 0));
        $tipo = $this->sanitizar($_POST['tipo'] ?? 'gasto');

        if (empty($nome) || strlen($nome) > 50) {
            $_SESSION['erro'] = "Nome da meta inválido.";
            redirecionar($id_meta > 0 ? "metas/editar/$id_meta" : 'metas/novo');
        }

        if (!$this->validarValor($valor)) {
            $_SESSION['erro'] = "Valor deve ser maior que zero.";
            redirecionar($id_meta > 0 ? "metas/editar/$id_meta" : 'metas/novo');
        }

        if ($tipo !== 'gasto' && $tipo !== 'reserva') {
            $_SESSION['erro'] = "Tipo de meta inválido.";
            redirecionar('metas');
        }

        $metaModel = $this->model('Meta');

        if ($id_meta > 0) {
            // Editar
            $meta = $metaModel->getMetaById($id_meta, $id_usuario);
            if (!$meta) {
                $_SESSION['erro'] = "Acesso negado.";
                redirecionar('metas');
            }
            $sucesso = $metaModel->atualizar($id_meta, $nome, $valor, $tipo);
            $mensagem = "Meta atualizada com sucesso!";
        } else {
            // Novo
            $sucesso = $metaModel->adicionar($id_usuario, $nome, $valor, $tipo);
            $mensagem = "Meta adicionada com sucesso!";
        }

        if ($sucesso) {
            $_SESSION['sucesso'] = $mensagem;
        } else {
            $_SESSION['erro'] = "Erro ao salvar meta.";
        }

        redirecionar('metas');
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
     * Adiciona meta (com suporte AJAX)
     */
    public function adicionar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirecionar('metas');
        }

        $id_usuario = $_SESSION['id_usuario'];
        $nome = $this->sanitizar($_POST['nome_meta'] ?? '');
        $valor = floatval(str_replace(',', '.', $_POST['valor_limite'] ?? 0));

        // Validações
        if (empty($nome) || strlen($nome) > 50) {
            if ($this->isAjax()) {
                $this->jsonResponse(false, 'Nome da meta inválido (máx 50 caracteres).');
            }
            $_SESSION['erro'] = "Nome da meta inválido.";
            redirecionar('metas/novo');
        }

        if ($valor <= 0) {
            if ($this->isAjax()) {
                $this->jsonResponse(false, 'Valor deve ser maior que zero.');
            }
            $_SESSION['erro'] = "Valor deve ser maior que zero.";
            redirecionar('metas/novo');
        }

        $metaModel = $this->model('Meta');
        if ($metaModel->adicionar($id_usuario, $nome, $valor)) {
            if ($this->isAjax()) {
                $this->jsonResponse(true, 'Meta adicionada com sucesso!', $this->getDashData($id_usuario));
            }
            $_SESSION['sucesso'] = "Meta adicionada com sucesso!";
        } else {
            if ($this->isAjax()) {
                $this->jsonResponse(false, 'Erro ao adicionar meta.');
            }
            $_SESSION['erro'] = "Erro ao adicionar meta.";
        }

        redirecionar('metas');
    }

    /**
     * Deleta uma meta
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
            redirecionar('metas');
        }

        $metaModel = $this->model('Meta');
        
        $meta = $metaModel->getMetaById($id, $id_usuario);
        if (!$meta) {
            if ($this->isAjax()) {
                $this->jsonResponse(false, 'Acesso negado.');
            }
            $_SESSION['erro'] = "Acesso negado.";
            redirecionar('metas');
        }

        if ($metaModel->deletar($id, $id_usuario)) {
            if ($this->isAjax()) {
                // ✅ CORREÇÃO: Agora retorna dados atualizados completos
                $this->jsonResponse(true, 'Meta deletada com sucesso!', $this->getDashData($id_usuario));
            }
            $_SESSION['sucesso'] = "Meta deletada com sucesso!";
        } else {
            if ($this->isAjax()) {
                $this->jsonResponse(false, 'Erro ao deletar meta.');
            }
            $_SESSION['erro'] = "Erro ao deletar meta.";
        }

        redirecionar('metas');
    }

    /**
     * Guarda dinheiro em uma meta
     */
    public function guardar($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirecionar('metas');
        }

        $id_usuario = $_SESSION['id_usuario'];
        $id = intval($id);

        // Validar CSRF
        if (empty($_POST['csrf_token']) || !$this->validarTokenCSRF($_POST['csrf_token'])) {
            $_SESSION['erro'] = "Solicitação inválida.";
            redirecionar('metas');
        }

        $valor = floatval(str_replace(',', '.', $_POST['valor'] ?? 0));

        if (!$this->validarValor($valor)) {
            $_SESSION['erro'] = "Valor inválido.";
            redirecionar('metas');
        }

        $metaModel = $this->model('Meta');
        $meta = $metaModel->getMetaById($id, $id_usuario);
        
        if (!$meta) {
            $_SESSION['erro'] = "Meta não encontrada.";
            redirecionar('metas');
        }

        if ($metaModel->guardarDinheiro($id, $id_usuario, $valor)) {
            $_SESSION['sucesso'] = "Dinheiro guardado na meta!";
        } else {
            $_SESSION['erro'] = "Erro ao guardar dinheiro.";
        }

        redirecionar('metas');
    }

    /**
     * Salva/edita meta (com suporte AJAX do dashboard)
     * 🔧 CORREÇÃO: Retorna dados atualizados para todas as operações AJAX
     */
    public function salvar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirecionar('metas');
        }

        $id_usuario = $_SESSION['id_usuario'];
        $id_meta    = intval($_POST['id_meta'] ?? 0);
        $nome       = $this->sanitizar($_POST['nome_meta'] ?? '');
        $valor      = floatval(str_replace(',', '.', $_POST['valor_limite'] ?? 0));

        if (empty($nome) || strlen($nome) > 50) {
            if ($this->isAjax()) { $this->jsonResponse(false, 'Nome da meta inválido (máx 50 caracteres).'); }
            $_SESSION['erro'] = "Nome da meta inválido.";
            redirecionar('metas');
        }

        if ($valor <= 0) {
            if ($this->isAjax()) { $this->jsonResponse(false, 'Valor deve ser maior que zero.'); }
            $_SESSION['erro'] = "Valor deve ser maior que zero.";
            redirecionar('metas');
        }

        $metaModel = $this->model('Meta');

        if ($id_meta > 0) {
            $meta = $metaModel->getMetaById($id_meta, $id_usuario);
            if (!$meta) {
                if ($this->isAjax()) { $this->jsonResponse(false, 'Acesso negado.'); }
                redirecionar('metas');
            }
            $sucesso  = $metaModel->atualizar($id_meta, $nome, $valor, $meta['tipo'] ?? 'gasto');
            $mensagem = 'Meta atualizada com sucesso!';
        } else {
            $sucesso  = $metaModel->adicionar($id_usuario, $nome, $valor);
            $mensagem = 'Meta adicionada com sucesso!';
        }

        if ($sucesso) {
            if ($this->isAjax()) { 
                // ✅ CORREÇÃO: Retorna dados atualizados do dashboard
                $this->jsonResponse(true, $mensagem, $this->getDashData($id_usuario)); 
            }
            $_SESSION['sucesso'] = $mensagem;
        } else {
            if ($this->isAjax()) { $this->jsonResponse(false, 'Erro ao salvar meta.'); }
            $_SESSION['erro'] = "Erro ao salvar meta.";
        }

        redirecionar('metas');
    }

    /**
     * ✅ MÉTODO CORRIGIDO: Retorna dados mínimos do dashboard para atualização em tempo real
     * Agora com melhor tratamento de erros e fallbacks para valores vazios
     */
    private function getDashData($id_usuario)
    {
        $gastoModel    = $this->model('Gasto');
        $metaModel     = $this->model('Meta');
        $salarioModel  = $this->model('Salario');
        $poupancaModel = $this->model('Poupanca');

        // Buscar dados com fallback para valores vazios
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
