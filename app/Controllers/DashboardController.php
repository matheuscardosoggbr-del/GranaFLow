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
        $receitaModel   = $this->model('Receita');
        $metaModel      = $this->model('Meta');
        $categoriaModel = $this->model('Categoria');
        $salarioModel   = $this->model('Salario');
        $poupancaModel  = $this->model('Poupanca');

        $gastoModel->gerarRecorrentes($id_usuario);

        $gastos      = $gastoModel->getGastos($id_usuario);
        $receitas    = $receitaModel->getReceitas($id_usuario);
        $metas       = $metaModel->getMetas($id_usuario);
        $categorias  = $categoriaModel->getCategorias($id_usuario);
        $salario     = $salarioModel->getSalario($id_usuario);
        $recorrentes = $gastoModel->getRecorrentes($id_usuario);
        $total_guardado = $poupancaModel->getTotalGuardado($id_usuario);
        $historico_guardado = $poupancaModel->getHistorico($id_usuario, 5);

        $mes_atual = date('m');
        $ano_atual = date('Y');

        $gastos_mes = array_filter($gastos, function ($g) use ($mes_atual, $ano_atual) {
            return date('m', strtotime($g['data_gasto'])) == $mes_atual
                && date('Y', strtotime($g['data_gasto'])) == $ano_atual;
        });
        $total_mes_pontuais = array_sum(array_column($gastos_mes, 'valor'));

        $receitas_mes = array_filter($receitas, function ($r) use ($mes_atual, $ano_atual) {
            return date('m', strtotime($r['data_receita'])) == $mes_atual
                && date('Y', strtotime($r['data_receita'])) == $ano_atual;
        });
        $total_receitas_mes = array_sum(array_column($receitas_mes, 'valor'));

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

        $saldo = $salario + $total_receitas_mes - $total_mes - $total_guardado;

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

            $receitas_ref = array_filter($receitas, function ($r) use ($mes_ref, $ano_ref) {
                return date('m', strtotime($r['data_receita'])) == $mes_ref
                    && date('Y', strtotime($r['data_receita'])) == $ano_ref;
            });
            $total_receitas_ref = array_sum(array_column($receitas_ref, 'valor'));

            if ($i === 0) {
                $total_ref += $total_recorrentes_pendentes;
                $saldo_ref  = $salario + $total_receitas_ref - $total_ref - $total_guardado;
            } else {
                $saldo_ref = $salario + $total_receitas_ref - $total_ref;
            }

            $grafico_saldo['labels'][]  = $nome_mes;
            $grafico_saldo['valores'][] = $saldo_ref;
        }

        $grafico_metas = [
            'labels'   => array_column($metas, 'nome_meta'),
            'limites'  => array_column($metas, 'valor_limite'),
            'guardado' => array_column($metas, 'valor_guardado'),
        ];

        $data = [
            'nome_usuario'       => $_SESSION['nome'],
            'csrf_token'         => $this->gerarTokenCSRF(),
            'salario'            => $salario,
            'total_mes'          => $total_mes,
            'total_receitas_mes' => $total_receitas_mes,
            'total_geral'        => $total_geral,
            'total_recorrentes'  => $total_recorrentes,
            'total_guardado'     => $total_guardado,
            'historico_guardado' => $historico_guardado,
            'saldo'              => $saldo,
            'gastos'             => $gastos,
            'receitas'           => $receitas,
            'metas'              => $metas,
            'categorias'         => $categorias,
            'recorrentes'        => $recorrentes,
            'grafico_saldo'      => $grafico_saldo,
            'grafico_metas'      => $grafico_metas,
        ];

        $this->view('dashboard/index', $data);
    }
public function getDashboardData()
    {
        if (!$this->isAjax()) {
            http_response_code(400);
            exit;
        }

        try {
            $data = $this->calcularDadosDashboard();
            $this->jsonResponse(true, 'Dados atualizados', $data);
        } catch (\Exception $e) {
            $this->jsonResponse(false, 'Erro ao carregar dados: ' . $e->getMessage());
        }
    }
private function calcularDadosDashboard()
    {
        $id_usuario     = $_SESSION['id_usuario'];
        $gastoModel     = $this->model('Gasto');
        $receitaModel   = $this->model('Receita');
        $metaModel      = $this->model('Meta');
        $salarioModel   = $this->model('Salario');
        $poupancaModel  = $this->model('Poupanca');

        $gastos         = $gastoModel->getGastos($id_usuario);
        $receitas       = $receitaModel->getReceitas($id_usuario);
        $metas          = $metaModel->getMetas($id_usuario);
        $salario        = $salarioModel->getSalario($id_usuario);
        $recorrentes    = $gastoModel->getRecorrentes($id_usuario);
        $total_guardado = $poupancaModel->getTotalGuardado($id_usuario);
        $historico_guardado = $poupancaModel->getHistorico($id_usuario, 5);

        $mes_atual = date('m');
        $ano_atual = date('Y');

        $gastos_mes = array_filter($gastos, function ($g) use ($mes_atual, $ano_atual) {
            return date('m', strtotime($g['data_gasto'])) == $mes_atual
                && date('Y', strtotime($g['data_gasto'])) == $ano_atual;
        });
        $total_mes_pontuais = array_sum(array_column($gastos_mes, 'valor'));
        $receitas_mes = array_filter($receitas, function ($r) use ($mes_atual, $ano_atual) {
            return date('m', strtotime($r['data_receita'])) == $mes_atual
                && date('Y', strtotime($r['data_receita'])) == $ano_atual;
        });
        $total_receitas_mes = array_sum(array_column($receitas_mes, 'valor'));

        $total_recorrentes_pendentes = 0;
        foreach ($recorrentes as $r) {
            $ultima    = $r['ultima_execucao'] ? date('Y-m', strtotime($r['ultima_execucao'])) : null;
            $ja_gerado = ($ultima === date('Y-m'));
            if (!$ja_gerado) {
                $total_recorrentes_pendentes += $r['valor'];
            }
        }

        $total_mes   = $total_mes_pontuais + $total_recorrentes_pendentes;
        $total_geral = array_sum(array_column($gastos, 'valor'));
        $saldo       = $salario + $total_receitas_mes - $total_mes - $total_guardado;

        return [
            'csrf_token'         => $this->gerarTokenCSRF(),
            'salario'            => $salario,
            'total_mes'          => $total_mes,
            'total_receitas_mes' => $total_receitas_mes,
            'total_geral'        => $total_geral,
            'total_guardado'     => $total_guardado,
            'saldo'              => $saldo,
            'recorrentes'        => $recorrentes,
            'metas'              => $metas,
            'gastos'             => $gastos,
            'receitas'           => $receitas,
            'historico_guardado' => $historico_guardado,
        ];
    }

    private function isAjax()
    {
        return isset($_POST['_ajax']) || isset($_GET['_ajax']) ||
               (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
    }

    private function jsonResponse($success, $message = '', $data = [], $action = '')
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data'    => array_merge((array)$data, ['action' => $action]),
            'action'  => $action,
        ]);
        exit;
    }

    public function salvarSalario()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['salario'])) {
            try {
                if (empty($_POST['csrf_token']) || !$this->validarTokenCSRF($_POST['csrf_token'])) {
                    if ($this->isAjax()) {
                        $this->jsonResponse(false, 'SolicitaÃ§Ã£o invÃ¡lida.');
                    }
                    redirecionar('dashboard');
                }

                $salarioModel = $this->model('Salario');
                $salarioModel->salvar($_POST['salario'], $_SESSION['id_usuario']);

                if ($this->isAjax()) {
                    $data = $this->calcularDadosDashboard();
                    $this->jsonResponse(true, 'SalÃ¡rio atualizado com sucesso!', $data, 'salario');
                }
            } catch (\Exception $e) {
                if ($this->isAjax()) {
                    $this->jsonResponse(false, 'Erro ao salvar salÃ¡rio: ' . $e->getMessage());
                }
            }
        }
        redirecionar('dashboard');
    }

    public function adicionarRecorrente()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                if (empty($_POST['csrf_token']) || !$this->validarTokenCSRF($_POST['csrf_token'])) {
                    if ($this->isAjax()) {
                        $this->jsonResponse(false, 'SolicitaÃ§Ã£o invÃ¡lida.');
                    }
                    redirecionar('dashboard');
                }

                $tipo = $this->sanitizar($_POST['tipo'] ?? 'mensal');
                $quantidade = null;

                if ($tipo === 'parcelado') {
                    $quantidade = intval($_POST['quantidade'] ?? 0);
                    if ($quantidade < 1) {
                        if ($this->isAjax()) {
                            $this->jsonResponse(false, 'Quantidade de meses invÃ¡lida.');
                        }
                        redirecionar('dashboard');
                    }
                }

                $gastoModel = $this->model('Gasto');
                $gastoModel->adicionarRecorrente(
                    $_SESSION['id_usuario'],
                    $_POST['id_categoria'],
                    $_POST['descricao'],
                    $_POST['valor'],
                    $_POST['dia_vencimento'],
                    $tipo,
                    $quantidade
                );

                if ($this->isAjax()) {
                    $data = $this->calcularDadosDashboard();
                    $this->jsonResponse(true, 'Gasto recorrente adicionado com sucesso!', $data, 'recorrente');
                }
            } catch (\Exception $e) {
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
                if (empty($_POST['csrf_token']) || !$this->validarTokenCSRF($_POST['csrf_token'])) {
                    if ($this->isAjax()) {
                        $this->jsonResponse(false, 'SolicitaÃ§Ã£o invÃ¡lida.');
                    }
                    redirecionar('dashboard');
                }

                $id_usuario = $_SESSION['id_usuario'];
                $valor      = floatval($_POST['valor'] ?? 0);
                $id_meta    = intval($_POST['id_meta'] ?? 0);

                if ($valor <= 0) {
                    if ($this->isAjax()) {
                        $this->jsonResponse(false, 'Valor deve ser maior que zero.');
                    }
                    redirecionar('dashboard');
                }

                $metaModel     = $this->model('Meta');
                $poupancaModel = $this->model('Poupanca');

                $meta = $metaModel->getMetaById($id_meta, $id_usuario);
                if (!$meta) {
                    if ($this->isAjax()) {
                        $this->jsonResponse(false, 'Meta nÃ£o encontrada.');
                    }
                    redirecionar('dashboard');
                }
                $metaModel->guardarDinheiro($id_meta, $id_usuario, $valor);
                $poupancaModel->guardar($id_usuario, $valor, 'Guardado para: ' . $meta['nome_meta']);

                if ($this->isAjax()) {
                    $data = $this->calcularDadosDashboard();
                    $this->jsonResponse(true, 'Dinheiro guardado na meta com sucesso!', $data, 'meta');
                }
            } catch (\Exception $e) {
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
                if (empty($_POST['csrf_token']) || !$this->validarTokenCSRF($_POST['csrf_token'])) {
                    if ($this->isAjax()) {
                        $this->jsonResponse(false, 'SolicitaÃ§Ã£o invÃ¡lida.');
                    }
                    redirecionar('dashboard');
                }

                $id_usuario    = $_SESSION['id_usuario'];
                $valor         = floatval($_POST['valor']);
                $id_meta       = intval($_POST['id_meta'] ?? 0);
                $descricao     = !empty($_POST['descricao']) ? $_POST['descricao'] : 'Dinheiro guardado';

                $poupancaModel = $this->model('Poupanca');
                $metaModel     = $this->model('Meta');
                if ($id_meta > 0) {
                    $meta = $metaModel->getMetaById($id_meta, $id_usuario);
                    if ($meta) {
                        $metaModel->guardarDinheiro($id_meta, $id_usuario, $valor);
                        $descricao = 'Guardado para: ' . $meta['nome_meta'];
                    }
                }
                $poupancaModel->guardar($id_usuario, $valor, $descricao);

                if ($this->isAjax()) {
                    $data = $this->calcularDadosDashboard();
                    $this->jsonResponse(true, 'Dinheiro guardado com sucesso!', $data, 'guardado');
                }
            } catch (\Exception $e) {
                if ($this->isAjax()) {
                    $this->jsonResponse(false, 'Erro ao guardar dinheiro: ' . $e->getMessage());
                }
            }
        }
        redirecionar('dashboard');
    }
public function deletarRecorrente($id = null)
    {
        $id_usuario = $_SESSION['id_usuario'];
        $id = intval($id ?? ($_GET['id'] ?? 0));

        if (empty($_POST['csrf_token']) || !$this->validarTokenCSRF($_POST['csrf_token'])) {
            if ($this->isAjax()) {
                $this->jsonResponse(false, 'SolicitaÃ§Ã£o invÃ¡lida.');
            }
            redirecionar('dashboard');
        }

        if ($id <= 0) {
            if ($this->isAjax()) {
                $this->jsonResponse(false, 'ID invÃ¡lido.');
            }
            redirecionar('dashboard');
        }

        $gastoModel = $this->model('Gasto');

        if ($gastoModel->deletarRecorrente($id, $id_usuario)) {
            if ($this->isAjax()) {
                $data = $this->calcularDadosDashboard();
                $this->jsonResponse(true, 'Gasto recorrente removido com sucesso!', $data, 'recorrente');
            }
        } else {
            if ($this->isAjax()) {
                $this->jsonResponse(false, 'Erro ao remover gasto recorrente.');
            }
        }
        redirecionar('dashboard');
    }
public function editarRecorrente($id = null)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirecionar('dashboard');
        }

        $id_usuario = $_SESSION['id_usuario'];
        $id = intval($id ?? ($_POST['id'] ?? 0));

        if (empty($_POST['csrf_token']) || !$this->validarTokenCSRF($_POST['csrf_token'])) {
            if ($this->isAjax()) {
                $this->jsonResponse(false, 'SolicitaÃ§Ã£o invÃ¡lida.');
            }
            redirecionar('dashboard');
        }

        if ($id <= 0) {
            if ($this->isAjax()) {
                $this->jsonResponse(false, 'ID invÃ¡lido.');
            }
            redirecionar('dashboard');
        }

        $descricao = trim($_POST['descricao'] ?? '');
        $valor     = floatval($_POST['valor'] ?? 0);
        $dia       = intval($_POST['dia_vencimento'] ?? 0);

        if (empty($descricao) || $valor <= 0 || $dia < 1 || $dia > 31) {
            if ($this->isAjax()) {
                $this->jsonResponse(false, 'Dados invÃ¡lidos.');
            }
            redirecionar('dashboard');
        }

        $gastoModel = $this->model('Gasto');

        if ($gastoModel->atualizarRecorrente($id, $id_usuario, $descricao, $valor, $dia)) {
            if ($this->isAjax()) {
                $data = $this->calcularDadosDashboard();
                $this->jsonResponse(true, 'Gasto recorrente atualizado!', $data, 'recorrente');
            }
        } else {
            if ($this->isAjax()) {
                $this->jsonResponse(false, 'Erro ao atualizar gasto recorrente.');
            }
        }
        redirecionar('dashboard');
    }
}

