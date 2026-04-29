<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorias — GranaFlow</title>
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

    <div class="container mt-4" style="max-width:900px; padding-bottom:60px;">
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

        <h2 class="mb-4"><i class="bi bi-tag me-2"></i>Categorias Personalizadas</h2>

        <!-- Formulário Nova Categoria -->
        <div class="card card-resumo mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">Adicionar Categoria</h5>
                <form method="POST" action="<?= BASE_URL ?>categoria/salvar">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <input type="text" name="nome" class="form-control" placeholder="Nome da categoria" 
                                   maxlength="30" required>
                        </div>
                        <div class="col-md-4">
                            <select name="id_tipo" class="form-select" required>
                                <option value="2">Despesa</option>
                                <option value="1">Receita</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-plus-circle me-1"></i>Adicionar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Lista de Categorias -->
        <?php if (empty($categorias)): ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>Você não tem categorias personalizadas. Use as categorias padrão.
            </div>
        <?php else: ?>
            <div class="card card-resumo">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Nome</th>
                                    <th>Tipo</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categorias as $cat): ?>
                                    <tr>
                                        <td>
                                            <strong><?= htmlspecialchars($cat['nome']) ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $cat['tipo_nome'] === 'Receita' ? 'success' : 'danger' ?>">
                                                <?= $cat['tipo_nome'] ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <form method="POST" action="<?= BASE_URL ?>categoria/deletar/<?= $cat['id_categoria'] ?>" 
                                                  style="display:inline;" onsubmit="return confirm('Deseja deletar esta categoria?');">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
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
</body>
</html>
