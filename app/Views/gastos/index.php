<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gastos — GranaFlow</title>
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
                        <a class="nav-link active" href="<?= BASE_URL ?>gastos">Gastos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>metas">Metas</a>
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
        <!-- Mensagens de sucesso/erro -->
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
            <h2><i class="bi bi-receipt me-2"></i>Gerenciar Gastos</h2>
            <a href="<?= BASE_URL ?>gastos/novo" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Novo Gasto
            </a>
        </div>

        <!-- Filtros -->
        <div class="card card-resumo mb-4">
            <div class="card-body">
                <form method="GET" class="row g-2">
                    <div class="col-md-4">
                        <label class="form-label">Categoria</label>
                        <select name="categoria" class="form-select">
                            <option value="">Todas as categorias</option>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?= $cat['id_categoria'] ?>" 
                                    <?= $filtro_categoria == $cat['id_categoria'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['nome']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Mês</label>
                        <input type="month" name="mes" class="form-control" value="<?= $filtro_mes ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Ordenar por</label>
                        <select name="ordem" class="form-select">
                            <option value="data_desc" <?= $ordem == 'data_desc' ? 'selected' : '' ?>>Data (mais recente)</option>
                            <option value="data_asc" <?= $ordem == 'data_asc' ? 'selected' : '' ?>>Data (mais antiga)</option>
                            <option value="valor_desc" <?= $ordem == 'valor_desc' ? 'selected' : '' ?>>Valor (maior)</option>
                            <option value="valor_asc" <?= $ordem == 'valor_asc' ? 'selected' : '' ?>>Valor (menor)</option>
                            <option value="categoria" <?= $ordem == 'categoria' ? 'selected' : '' ?>>Categoria</option>
                        </select>
                    </div>
                    <div class="col-12 d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-search me-1"></i>Filtrar
                        </button>
                        <a href="<?= BASE_URL ?>gastos" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>Limpar
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabela de Gastos -->
        <?php if (empty($gastos)): ?>
            <div class="alert alert-info text-center">
                <i class="bi bi-info-circle me-2"></i>Nenhum gasto encontrado.
            </div>
        <?php else: ?>
            <div class="card card-resumo">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Data</th>
                                    <th>Descrição</th>
                                    <th>Categoria</th>
                                    <th class="text-end">Valor</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($gastos as $gasto): ?>
                                    <tr>
                                        <td><?= date('d/m/Y', strtotime($gasto['data_gasto'])) ?></td>
                                        <td><?= htmlspecialchars($gasto['descricao'] ?? '—') ?></td>
                                        <td>
                                            <span class="badge bg-light text-dark">
                                                <?= htmlspecialchars($gasto['categoria']) ?>
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <span class="text-danger fw-bold">
                                                <?= $gasto['simbolo'] ?> <?= number_format($gasto['valor'], 2, ',', '.') ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="<?= BASE_URL ?>gastos/editar/<?= $gasto['id_gasto'] ?>" 
                                               class="btn btn-sm btn-outline-primary" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form method="POST" action="<?= BASE_URL ?>gastos/deletar/<?= $gasto['id_gasto'] ?>" 
                                                  style="display:inline;" onsubmit="return confirm('Deseja deletar este gasto?');">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Deletar">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Total de Gastos:</strong> <?= number_format($total, 2, ',', '.') ?>
                        </div>
                        <div class="col-md-6 text-end">
                            <strong>Quantidade:</strong> <?= count($gastos) ?> gasto(s)
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
