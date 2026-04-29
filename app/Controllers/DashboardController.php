<?php

namespace App\Controllers;

use App\Core\Controller;

class DashboardController extends Controller
{
    public function __construct()
    {
        if (!isset($_SESSION['id_usuario'])) {
            redirecionar('auth/login');
        }
    }

    public function index()
    {
        $id_usuario     = $_SESSION['id_usuario'];
        $gastoModel     = $this->model('Gasto');
        $metaModel      = $this->model('Meta');
        $categoriaModel = $this->model('Categoria');
        $salarioModel   = $this->model('Salario');
        $poupancaModel  = $this->model('Poupanca');

        $gastoModel->gerarRecorrentes($id_usuario);

        $gastos      = $gastoModel->getGastos($id_usuario);
        $metas       = $metaModel->getMetas($id_usuario);
        $categorias  = $categoriaModel->getCategorias($id_usuario);
        $salario     = $salarioModel->getSalario($id_usuario);
        $recorrentes = $gastoModel->getRecorrentes($id_usuario);
        $total_guardado = $poupancaModel->getTotalGuardado($id_usuario);
        $historico_guardado = $poupancaModel->getHistorico($id_usuario, 5);

        $mes_atual = date('m');
        $ano_atual = date('Y');

        // Gastos pontuais do mês atual
        $gastos_mes = array_filter($gastos, function ($g) use ($mes_atual, $ano_atual) {
            return date('m', strtotime($g['data_gasto'])) == $mes_atual
                && date('Y', strtotime($g['data_gasto'])) == $ano_atual;
        });
        $total_mes_pontuais = array_sum(array_column($gastos_mes, 'valor'));

        // Recorrentes pendentes (ainda não gerados este mês)
        $total_recorrentes_pendentes = 0;
        foreach ($recorrentes as $r) {
            $ultima   = $r['ultima_execucao'] ? date('Y-m', strtotime($r['ultima_execucao'])) : null;
            $ja_gerado = ($ultima === date('Y-m'));
            if (!$ja_gerado) {
                $total_recorrentes_pendentes += $r['valor'];
            }
        }

        $total_recorrentes = array_sum(array_column($recorrentes, 'valor'));
        $total_mes         = $total_mes_pontuais + $total_recorrentes_pendentes;
        $total_geral       = array_sum(array_column($gastos, 'valor'));

        // Saldo = Salário - Gastos do mês - Dinheiro guardado
        $saldo = $salario - $total_mes - $total_guardado;

        // Gráfico de Saldo Mensal (Últimos 6 meses)
        $grafico_saldo = [];
        for ($i = 5; $i >= 0; $i--) {
            $mes_ref  = date('m', strtotime("-$i months"));
            $ano_ref  = date('Y', strtotime("-$i months"));
            $nome_mes = date('M', strtotime("-$i months"));

            $gastos_ref = array_filter($gastos, function ($g) use ($mes_ref, $ano_ref) {
                return date('m', strtotime($g['data_gasto'])) == $mes_ref
                    && date('Y', strtotime($g['data_gasto'])) == $ano_ref;
            });
            $total_ref = array_sum(array_column($gastos_ref, 'valor'));

            if ($i === 0) {
                $total_ref += $total_recorrentes_pendentes;
                $saldo_ref  = $salario - $total_ref - $total_guardado;
            } else {
                $saldo_ref = $salario - $total_ref;
            }

            $grafico_saldo['labels'][]  = $nome_mes;
            $grafico_saldo['valores'][] = $saldo_ref;
        }

        // Gráfico de Metas
        $grafico_metas = [
            'labels'   => array_column($metas, 'nome_meta'),
            'limites'  => array_column($metas, 'valor_limite'),
            'guardado' => array_column($metas, 'valor_guardado'),
        ];

        $data = [
            'nome_usuario'       => $_SESSION['nome'],
            'salario'            => $salario,
            'total_mes'          => $total_mes,
            'total_geral'        => $total_geral,
            'total_recorrentes'  => $total_recorrentes,
            'total_guardado'     => $total_guardado,
            'historico_guardado' => $historico_guardado,
            'saldo'              => $saldo,
            'gastos'             => $gastos,
            'metas'              => $metas,
            'categorias'         => $categorias,
            'recorrentes'        => $recorrentes,
            'grafico_saldo'      => $grafico_saldo,
            'grafico_metas'      => $grafico_metas,
        ];

        $this->view('dashboard/index', $data);
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
     * Retorna resposta JSON e interrompe
     */
    private function jsonResponse($success, $message = '', $data = [], $action = '')
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data' => array_merge($data, ['action' => $action]),
            'action' => $action
        ]);
        exit;
    }

    /**
     * Recalcula dados do dashboard
     */
    private function getDashboardData()
    {
        $id_usuario     = $_SESSION['id_usuario'];
        $gastoModel     = $this->model('Gasto');
        $metaModel      = $this->model('Meta');
        $categoriaModel = $this->model('Categoria');
        $salarioModel   = $this->model('Salario');
        $poupancaModel  = $this->model('Poupanca');

        $gastos         = $gastoModel->getGastos($id_usuario);
        $metas          = $metaModel->getMetas($id_usuario);
        $salario        = $salarioModel->getSalario($id_usuario);
        $recorrentes    = $gastoModel->getRecorrentes($id_usuario);
        $total_guardado = $poupancaModel->getTotalGuardado($id_usuario);

        $mes_atual = date('m');
        $ano_atual = date('Y');

        $gastos_mes = array_filter($gastos, function ($g) use ($mes_atual, $ano_atual) {
            return date('m', strtotime($g['data_gasto'])) == $mes_atual
                && date('Y', strtotime($g['data_gasto'])) == $ano_atual;
        });
        $total_mes_pontuais = array_sum(array_column($gastos_mes, 'valor'));

        $total_recorrentes_pendentes = 0;
        foreach ($recorrentes as $r) {
            $ultima   = $r['ultima_execucao'] ? date('Y-m', strtotime($r['ultima_execucao'])) : null;
            $ja_gerado = ($ultima === date('Y-m'));
            if (!$ja_gerado) {
                $total_recorrentes_pendentes += $r['valor'];
            }
        }

        $total_mes   = $total_mes_pontuais + $total_recorrentes_pendentes;
        $total_geral = array_sum(array_column($gastos, 'valor'));
        $saldo       = $salario - $total_mes - $total_guardado;

        return [
            'salario'        => $salario,
            'total_mes'      => $total_mes,
            'total_geral'    => $total_geral,
            'total_guardado' => $total_guardado,
            'saldo'          => $saldo,
            'recorrentes'    => $recorrentes,
            'metas'          => $metas,
        ];
    }

    public function salvarSalario()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['salario'])) {
            try {
                $salarioModel = $this->model('Salario');
                $salarioModel->salvar($_POST['salario'], $_SESSION['id_usuario']);

                if ($this->isAjax()) {
                    $data = $this->getDashboardData();
                    $this->jsonResponse(true, 'Salário atualizado com sucesso!', $data, 'salario');
                }
            } catch (Exception $e) {
                if ($this->isAjax()) {
                    $this->jsonResponse(false, 'Erro ao salvar salário: ' . $e->getMessage());
                }
            }
        }
        redirecionar('dashboard');
    }

    public function adicionarRecorrente()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $gastoModel = $this->model('Gasto');
                $gastoModel->adicionarRecorrente(
                    $_SESSION['id_usuario'],
                    $_POST['id_categoria'],
                    $_POST['descricao'],
                    $_POST['valor'],
                    $_POST['dia_vencimento'],
                    $_POST['tipo'],
                    $_POST['quantidade'] ?? null
                );

                if ($this->isAjax()) {
                    $data = $this->getDashboardData();
                    $this->jsonResponse(true, 'Gasto recorrente adicionado com sucesso!', $data, 'recorrente');
                }
            } catch (Exception $e) {
                if ($this->isAjax()) {
                    $this->jsonResponse(false, 'Erro ao adicionar gasto recorrente: ' . $e->getMessage());
                }
            }
        }
        redirecionar('dashboard');
    }

    public function guardarDinheiro()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $metaModel = $this->model('Meta');
                $metaModel->guardarDinheiro($_POST['id_meta'], $_SESSION['id_usuario'], $_POST['valor']);

                if ($this->isAjax()) {
                    $data = $this->getDashboardData();
                    $this->jsonResponse(true, 'Dinheiro guardado com sucesso!', $data, 'meta');
                }
            } catch (Exception $e) {
                if ($this->isAjax()) {
                    $this->jsonResponse(false, 'Erro ao guardar dinheiro: ' . $e->getMessage());
                }
            }
        }
        redirecionar('dashboard');
    }

    public function guardarAvulso()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['valor']) && $_POST['valor'] > 0) {
            try {
                $poupancaModel = $this->model('Poupanca');
                $descricao = !empty($_POST['descricao']) ? $_POST['descricao'] : 'Dinheiro guardado';
                $poupancaModel->guardar($_SESSION['id_usuario'], $_POST['valor'], $descricao);

                if ($this->isAjax()) {
                    $data = $this->getDashboardData();
                    $this->jsonResponse(true, 'Dinheiro guardado com sucesso!', $data, 'guardado');
                }
            } catch (Exception $e) {
                if ($this->isAjax()) {
                    $this->jsonResponse(false, 'Erro ao guardar dinheiro: ' . $e->getMessage());
                }
            }
        }
        redirecionar('dashboard');
    }
}
