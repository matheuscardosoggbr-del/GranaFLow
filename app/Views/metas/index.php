<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Metas — GranaFlow</title>
    <script src="<?= BASE_URL ?>js/theme.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
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
                        <a class="nav-link active" href="<?= BASE_URL ?>metas">Metas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>salario">Salário</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>auth/logout">Sair</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4" style="max-width:1200px; padding-bottom:60px;">
        <!-- Mensagens -->
        <?php if (!empty($_SESSION['sucesso'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle me-2"></i>
                <?= htmlspecialchars($_SESSION['sucesso']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['sucesso']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['erro'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-circle me-2"></i>
                <?= htmlspecialchars($_SESSION['erro']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['erro']); ?>
        <?php endif; ?>

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-bullseye me-2"></i>Minhas Metas</h2>
            <a href="<?= BASE_URL ?>metas/novo" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Nova Meta
            </a>
        </div>

        <!-- Grid de Metas -->
        <?php if (empty($metas)): ?>
            <div class="alert alert-info text-center">
                <i class="bi bi-info-circle me-2"></i>Você ainda não criou nenhuma meta.
            </div>
        <?php else: ?>
            <div class="row g-3">
                <?php foreach ($metas as $meta): 
                    $percentual = $meta['valor_limite'] > 0 ? ($meta['valor_guardado'] / $meta['valor_limite'] * 100) : 0;
                    $cor_progresso = $percentual >= 100 ? 'success' : ($percentual >= 50 ? 'info' : 'warning');
                    $tipo_badge = $meta['tipo'] === 'gasto' ? 'danger' : 'success';
                ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card card-resumo h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="card-title"><?= htmlspecialchars($meta['nome_meta']) ?></h6>
                                    <span class="badge bg-<?= $tipo_badge ?> ">
                                        <?= $meta['tipo'] === 'gasto' ? 'Controle' : 'Reserva' ?>
                                    </span>
                                </div>

                                <!-- Barra de Progresso -->
                                <div class="mb-3">
                                    <div class="progress" style="height: 25px;">
                                        <div class="progress-bar bg-<?= $cor_progresso ?>" role="progressbar" 
                                             style="width: <?= min(100, $percentual) ?>%;" 
                                             aria-valuenow="<?= $percentual ?>" aria-valuemin="0" aria-valuemax="100">
                                            <?= number_format($percentual, 0) ?>%
                                        </div>
                                    </div>
                                </div>

                                <!-- Valores -->
                                <div class="row text-center mb-3">
                                    <div class="col-6">
                                        <small class="text-muted">Guardado</small>
                                        <div class="fw-bold text-success">
                                            R$ <?= number_format($meta['valor_guardado'], 2, ',', '.') ?>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Meta</small>
                                        <div class="fw-bold text-primary">
                                            R$ <?= number_format($meta['valor_limite'], 2, ',', '.') ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Formulário de Guardar Dinheiro -->
                                <?php if ($meta['tipo'] === 'reserva'): ?>
                                    <form method="POST" action="<?= BASE_URL ?>metas/guardar/<?= $meta['id_meta'] ?>" class="mb-2">
                                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                        <div class="input-group input-group-sm mb-2">
                                            <input type="number" name="valor" class="form-control" step="0.01" min="0.01" placeholder="Valor" required>
                                            <button type="submit" class="btn btn-sm btn-success" title="Guardar">
                                                <i class="bi bi-plus"></i>
                                            </button>
                                        </div>
                                    </form>
                                <?php endif; ?>

                                <!-- Botões -->
                                <div class="d-flex gap-2">
                                    <a href="<?= BASE_URL ?>metas/editar/<?= $meta['id_meta'] ?>" 
                                       class="btn btn-sm btn-outline-primary flex-grow-1">
                                        <i class="bi bi-pencil me-1"></i>Editar
                                    </a>
                                    <form method="POST" action="<?= BASE_URL ?>metas/deletar/<?= $meta['id_meta'] ?>" 
                                          style="display:inline; flex-grow:1;" onsubmit="return confirm('Deseja deletar esta meta?');">
                                        <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                            <i class="bi bi-trash me-1"></i>Deletar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
