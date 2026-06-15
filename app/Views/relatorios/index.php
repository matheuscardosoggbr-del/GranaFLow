<?php
$pageTitle = 'Relatórios';
$pageKicker = 'Finance CRM';
$pageHeading = 'Relatórios';
$activePage = 'relatorios';
require_once dirname(__DIR__) . '/partials/shell_top.php';
?>

<div class="d-flex justify-content-end mb-3">
    <div class="btn-group">
        <a href="<?= BASE_URL ?>relatorio/exportarCSV" class="btn btn-success btn-acao btn-sm">
            <i class="bi bi-file-earmark-text me-1"></i>Exportar CSV
        </a>
        <a href="<?= BASE_URL ?>relatorio/exportarJSON" class="btn btn-info btn-acao btn-sm">
            <i class="bi bi-file-earmark-code me-1"></i>Exportar JSON
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card card-resumo"><div class="card-body"><div class="card-resumo-icon" style="background:rgba(248,113,113,0.1);color:var(--red);"><i class="bi bi-graph-up"></i></div><h6>Total de Gastos</h6><div class="valor">R$ <?= number_format($total_gastos, 2, ',', '.') ?></div><small class="page-subtitle"><?= $quantidade_gastos ?> transações</small></div></div></div>
    <div class="col-md-3"><div class="card card-resumo"><div class="card-body"><div class="card-resumo-icon" style="background:rgba(251,191,36,0.1);color:var(--yellow);"><i class="bi bi-calendar3"></i></div><h6>Gasto este Mês</h6><div class="valor">R$ <?= number_format($total_mes, 2, ',', '.') ?></div><small class="page-subtitle"><?= $quantidade_mes ?> transações</small></div></div></div>
    <div class="col-md-3"><div class="card card-resumo"><div class="card-body"><div class="card-resumo-icon" style="background:rgba(52,211,153,0.1);color:var(--green);"><i class="bi bi-bullseye"></i></div><h6>Metas Atingidas</h6><div class="valor"><?= $metas_atingidas ?>/<?= $total_metas ?></div><small class="page-subtitle"><?= $total_metas > 0 ? number_format(($metas_atingidas / $total_metas * 100), 0) : 0 ?>% concluídas</small></div></div></div>
    <div class="col-md-3"><div class="card card-resumo"><div class="card-body"><div class="card-resumo-icon" style="background:rgba(124,106,247,0.12);color:var(--accent);"><i class="bi bi-ticket-detailed"></i></div><h6>Média por Transação</h6><div class="valor">R$ <?= $quantidade_gastos > 0 ? number_format($total_gastos / $quantidade_gastos, 2, ',', '.') : '0,00' ?></div><small class="page-subtitle">De todas as transações</small></div></div></div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6"><div class="card formulario-card"><div class="card-body"><h6><i class="bi bi-pie-chart me-2"></i>Gastos por Categoria</h6><canvas id="graficoCategoria" style="max-height:300px;"></canvas></div></div></div>
    <div class="col-md-6"><div class="card formulario-card"><div class="card-body"><h6><i class="bi bi-bar-chart me-2"></i>Top 5 Categorias</h6><div class="list-group list-group-flush"><?php $top5 = array_slice($gastos_categoria, 0, 5, true); $total_categoria = array_sum($gastos_categoria); foreach ($top5 as $categoria => $valor): $percentual = $total_categoria > 0 ? ($valor / $total_categoria * 100) : 0; ?><div class="list-group-item d-flex justify-content-between align-items-center"><span><?= htmlspecialchars($categoria) ?></span><span class="badge bg-primary"><?= number_format($percentual, 1) ?>%</span></div><?php endforeach; ?></div></div></div></div>
</div>

<?php if (!empty($metas)): ?>
    <div class="card formulario-card">
        <div class="card-body">
            <h6 class="mb-3"><i class="bi bi-list me-2"></i>Status das Metas</h6>
            <div class="table-responsive">
                <table class="table table-sm mb-0 tabela-gastos">
                    <thead>
                        <tr>
                            <th>Meta</th>
                            <th>Tipo</th>
                            <th class="text-end">Guardado</th>
                            <th class="text-end">Limite</th>
                            <th>Progresso</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($metas as $meta):
                            $percentual = $meta['valor_limite'] > 0 ? ($meta['valor_guardado'] / $meta['valor_limite'] * 100) : 0;
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($meta['nome_meta']) ?></td>
                                <td><span class="badge bg-<?= $meta['tipo'] === 'gasto' ? 'danger' : 'success' ?>"><?= $meta['tipo'] === 'gasto' ? 'Controle' : 'Reserva' ?></span></td>
                                <td class="text-end">R$ <?= number_format($meta['valor_guardado'], 2, ',', '.') ?></td>
                                <td class="text-end">R$ <?= number_format($meta['valor_limite'], 2, ',', '.') ?></td>
                                <td><div class="progress" style="height:20px;"><div class="progress-bar" role="progressbar" style="width: <?= min(100, $percentual) ?>%;"><?= number_format($percentual, 0) ?>%</div></div></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const labels = <?= json_encode(array_keys($gastos_categoria)) ?>;
const valores = <?= json_encode(array_values($gastos_categoria)) ?>;
const cores = ['#7c6af7', '#f87171', '#fbbf24', '#34d399', '#3b82f6', '#8b5cf6', '#ec4899', '#14b8a6', '#f59e0b', '#10b981'];
const ctxCategoria = document.getElementById('graficoCategoria');
if (ctxCategoria) {
    new Chart(ctxCategoria, {
        type: 'doughnut',
        data: { labels, datasets: [{ data: valores, backgroundColor: cores.slice(0, labels.length), borderColor: '#fff', borderWidth: 2 }] },
        options: { responsive: true, maintainAspectRatio: true }
    });
}
</script>

<?php require_once dirname(__DIR__) . '/partials/shell_bottom.php'; ?>
