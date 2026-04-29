<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatórios — GranaFlow</title>
    <script src="<?= BASE_URL ?>js/theme.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/Style.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand navbar-brand-new" href="<?= BASE_URL ?>dashboard">
                <span class="navbar-brand-text">GranaFlow</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>dashboard">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>gastos">Gastos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>metas">Metas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?= BASE_URL ?>relatorio">Relatórios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>auth/logout">Sair</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4" style="max-width:1200px; padding-bottom:60px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-bar-chart me-2"></i>Relatórios</h2>
            <div class="btn-group">
                <a href="<?= BASE_URL ?>relatorio/exportarCSV" class="btn btn-success btn-sm">
                    <i class="bi bi-file-earmark-text me-1"></i>Exportar CSV
                </a>
                <a href="<?= BASE_URL ?>relatorio/exportarJSON" class="btn btn-info btn-sm">
                    <i class="bi bi-file-earmark-code me-1"></i>Exportar JSON
                </a>
            </div>
        </div>

        <!-- Cards Resumo -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card card-resumo">
                    <div class="card-body">
                        <div class="card-resumo-icon" style="background:rgba(248,113,113,0.1);color:var(--red);">
                            <i class="bi bi-graph-up"></i>
                        </div>
                        <h6>Total de Gastos</h6>
                        <div class="valor">R$ <?= number_format($total_gastos, 2, ',', '.') ?></div>
                        <small class="text-muted"><?= $quantidade_gastos ?> transações</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-resumo">
                    <div class="card-body">
                        <div class="card-resumo-icon" style="background:rgba(251,191,36,0.1);color:var(--yellow);">
                            <i class="bi bi-calendar3"></i>
                        </div>
                        <h6>Gasto este Mês</h6>
                        <div class="valor">R$ <?= number_format($total_mes, 2, ',', '.') ?></div>
                        <small class="text-muted"><?= $quantidade_mes ?> transações</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-resumo">
                    <div class="card-body">
                        <div class="card-resumo-icon" style="background:rgba(52,211,153,0.1);color:var(--green);">
                            <i class="bi bi-bullseye"></i>
                        </div>
                        <h6>Metas Atingidas</h6>
                        <div class="valor"><?= $metas_atingidas ?>/<?= $total_metas ?></div>
                        <small class="text-muted">
                            <?= $total_metas > 0 ? number_format(($metas_atingidas / $total_metas * 100), 0) : 0 ?>% concluídas
                        </small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-resumo">
                    <div class="card-body">
                        <div class="card-resumo-icon" style="background:rgba(124,106,247,0.12);color:var(--accent);">
                            <i class="bi bi-ticket-detailed"></i>
                        </div>
                        <h6>Média por Transação</h6>
                        <div class="valor">
                            R$ <?= $quantidade_gastos > 0 ? number_format($total_gastos / $quantidade_gastos, 2, ',', '.') : '0,00' ?>
                        </div>
                        <small class="text-muted">De todas as transações</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card card-resumo">
                    <div class="card-body">
                        <h6><i class="bi bi-pie-chart me-2"></i>Gastos por Categoria</h6>
                        <canvas id="graficoCategoria" style="max-height: 300px;"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card card-resumo">
                    <div class="card-body">
                        <h6><i class="bi bi-bar-chart me-2"></i>Top 5 Categorias</h6>
                        <div class="list-group list-group-flush">
                            <?php 
                                $top5 = array_slice($gastos_categoria, 0, 5, true);
                                $total_categoria = array_sum($gastos_categoria);
                                foreach ($top5 as $categoria => $valor): 
                                    $percentual = ($valor / $total_categoria * 100);
                            ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><?= htmlspecialchars($categoria) ?></span>
                                    <span class="badge bg-primary"><?= number_format($percentual, 1) ?>%</span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabela de Metas -->
        <?php if (!empty($metas)): ?>
            <div class="card card-resumo">
                <div class="card-body">
                    <h6 class="mb-3"><i class="bi bi-list me-2"></i>Status das Metas</h6>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
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
                                        <td>
                                            <span class="badge bg-<?= $meta['tipo'] === 'gasto' ? 'danger' : 'success' ?>">
                                                <?= $meta['tipo'] === 'gasto' ? 'Controle' : 'Reserva' ?>
                                            </span>
                                        </td>
                                        <td class="text-end">R$ <?= number_format($meta['valor_guardado'], 2, ',', '.') ?></td>
                                        <td class="text-end">R$ <?= number_format($meta['valor_limite'], 2, ',', '.') ?></td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar" role="progressbar" 
                                                     style="width: <?= min(100, $percentual) ?>%;">
                                                    <?= number_format($percentual, 0) ?>%
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Gráfico de Categorias
        const labels = <?= json_encode(array_keys($gastos_categoria)) ?>;
        const valores = <?= json_encode(array_values($gastos_categoria)) ?>;
        const cores = [
            '#7c6af7', '#f87171', '#fbbf24', '#34d399', '#3b82f6',
            '#8b5cf6', '#ec4899', '#14b8a6', '#f59e0b', '#10b981'
        ];

        const ctxCategoria = document.getElementById('graficoCategoria');
        if (ctxCategoria) {
            new Chart(ctxCategoria, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: valores,
                        backgroundColor: cores.slice(0, labels.length),
                        borderColor: '#fff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });
        }
    </script>
</body>
</html>
