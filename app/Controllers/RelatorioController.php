<?php

namespace App\Controllers;

use App\Core\Controller;

class RelatorioController extends Controller
{
    public function __construct()
    {
        if (!isset($_SESSION['id_usuario'])) {
            redirecionar('auth/login');
        }
    }

    /**
     * Exibe página de relatórios
     */
    public function index()
    {
        $id_usuario = $_SESSION['id_usuario'];
        $gastoModel = $this->model('Gasto');
        $receitaModel = $this->model('Receita');
        $metaModel = $this->model('Meta');
        $salarioModel = $this->model('Salario');

        $gastos = $gastoModel->getGastos($id_usuario);
        $receitas = $receitaModel->getReceitas($id_usuario);
        $metas = $metaModel->getMetas($id_usuario);
        $salario = $salarioModel->getSalario($id_usuario);

        // Estatísticas
        $total_gastos = array_sum(array_column($gastos, 'valor'));
        $mes_atual = date('m');
        $ano_atual = date('Y');

        $gastos_mes = array_filter($gastos, function ($g) use ($mes_atual, $ano_atual) {
            return date('m', strtotime($g['data_gasto'])) == $mes_atual
                && date('Y', strtotime($g['data_gasto'])) == $ano_atual;
        });
        $total_mes = array_sum(array_column($gastos_mes, 'valor'));
        $receitas_mes = array_filter($receitas, function ($r) use ($mes_atual, $ano_atual) {
            return date('m', strtotime($r['data_receita'])) == $mes_atual
                && date('Y', strtotime($r['data_receita'])) == $ano_atual;
        });
        $total_receitas_mes = array_sum(array_column($receitas_mes, 'valor'));
        $saldo_estimado = $salario + $total_receitas_mes - $total_mes;
        $percentual_consumido = $salario > 0 ? min(100, ($total_mes / $salario) * 100) : 0;

        // Gastos por categoria
        $gastos_categoria = [];
        foreach ($gastos as $g) {
            if (!isset($gastos_categoria[$g['categoria']])) {
                $gastos_categoria[$g['categoria']] = 0;
            }
            $gastos_categoria[$g['categoria']] += $g['valor'];
        }
        arsort($gastos_categoria);

        // Progresso de metas
        $total_metas = count($metas);
        $metas_atingidas = count(array_filter($metas, function ($m) {
            return $m['valor_guardado'] >= $m['valor_limite'];
        }));

        $data = [
            'total_gastos' => $total_gastos,
            'total_mes' => $total_mes,
            'total_receitas_mes' => $total_receitas_mes,
            'saldo_estimado' => $saldo_estimado,
            'percentual_consumido' => $percentual_consumido,
            'salario' => $salario,
            'quantidade_gastos' => count($gastos),
            'quantidade_receitas' => count($receitas),
            'quantidade_mes' => count($gastos_mes),
            'gastos_categoria' => $gastos_categoria,
            'metas' => $metas,
            'total_metas' => $total_metas,
            'metas_atingidas' => $metas_atingidas,
            'gastos' => $gastos,
        ];

        $this->view('relatorios/index', $data);
    }

    /**
     * Exporta relatório em CSV
     */
    public function exportarCSV()
    {
        $id_usuario = $_SESSION['id_usuario'];
        $gastoModel = $this->model('Gasto');
        $gastos = $gastoModel->getGastos($id_usuario);

        // Headers para download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="relatorio_gastos_' . date('Y-m-d') . '.csv"');

        // BOM para UTF-8
        echo "\xEF\xBB\xBF";

        // Cabeçalho
        echo "Data,Descrição,Categoria,Valor\n";

        // Dados
        $sanitizarCsv = function ($valor) {
            $valor = (string) $valor;
            $valor = str_replace(["\r", "\n"], [' ', ' '], $valor);
            if (preg_match('/^[=+\-@]/', $valor)) {
                $valor = "'" . $valor;
            }
            return $valor;
        };

        foreach ($gastos as $gasto) {
            $data = date('d/m/Y', strtotime($gasto['data_gasto']));
            $descricao = $sanitizarCsv(htmlspecialchars_decode($gasto['descricao']));
            $categoria = $sanitizarCsv(htmlspecialchars_decode($gasto['categoria']));
            $valor = str_replace('.', ',', $gasto['valor']);

            echo "\"$data\",\"$descricao\",\"$categoria\",$valor\n";
        }

        exit;
    }

    /**
     * Exporta relatório em JSON
     */
    public function exportarJSON()
    {
        $id_usuario = $_SESSION['id_usuario'];
        $gastoModel = $this->model('Gasto');
        $receitaModel = $this->model('Receita');
        $metaModel = $this->model('Meta');
        $salarioModel = $this->model('Salario');

        $salario = $salarioModel->getSalario($id_usuario);
        $gastos = $gastoModel->getGastos($id_usuario);
        $receitas = $receitaModel->getReceitas($id_usuario);
        $metas = $metaModel->getMetas($id_usuario);
        $total_gastos = array_sum(array_column($gastos, 'valor'));
        $mes_atual = date('m');
        $ano_atual = date('Y');
        $gastos_mes = array_filter($gastos, function ($g) use ($mes_atual, $ano_atual) {
            return date('m', strtotime($g['data_gasto'])) == $mes_atual
                && date('Y', strtotime($g['data_gasto'])) == $ano_atual;
        });
        $total_mes = array_sum(array_column($gastos_mes, 'valor'));
        $receitas_mes = array_filter($receitas, function ($r) use ($mes_atual, $ano_atual) {
            return date('m', strtotime($r['data_receita'])) == $mes_atual
                && date('Y', strtotime($r['data_receita'])) == $ano_atual;
        });
        $total_receitas_mes = array_sum(array_column($receitas_mes, 'valor'));

        $dados = [
            'data_exportacao' => date('Y-m-d H:i:s'),
            'usuario' => $_SESSION['nome'],
            'salario_mensal' => $salario,
            'gastos' => $gastos,
            'receitas' => $receitas,
            'metas' => $metas,
            'resumo' => [
                'total_gastos' => $total_gastos,
                'total_mes' => $total_mes,
                'total_receitas_mes' => $total_receitas_mes,
                'saldo_estimado' => $salario + $total_receitas_mes - $total_mes,
                'quantidade_gastos' => count($gastos),
                'quantidade_receitas' => count($receitas),
            ]
        ];

        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="relatorio_gastos_' . date('Y-m-d') . '.json"');
        echo json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
}
