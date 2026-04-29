<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salário — GranaFlow</title>
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
                        <a class="nav-link active" href="<?= BASE_URL ?>salario">Salário</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>auth/logout">Sair</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4" style="max-width:600px; padding-bottom:60px;">
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

        <h2 class="mb-4"><i class="bi bi-cash-stack me-2"></i>Meu Salário</h2>

        <div class="card card-resumo">
            <div class="card-body">
                <form method="POST" action="<?= BASE_URL ?>salario/salvar">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                    <!-- Saldo Atual -->
                    <div class="alert alert-info mb-4">
                        <div class="text-center">
                            <small class="text-muted">Salário Atual</small>
                            <h3 class="mb-0 text-primary">
                                R$ <?= number_format($salario, 2, ',', '.') ?>
                            </h3>
                        </div>
                    </div>

                    <!-- Formulário -->
                    <div class="mb-3">
                        <label for="valor" class="form-label">Novo Salário <span class="text-danger">*</span></label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text">R$</span>
                            <input type="number" id="valor" name="valor" class="form-control" step="0.01" min="0.01"
                                   value="<?= number_format($salario, 2, '.', '') ?>"
                                   placeholder="0,00" required>
                        </div>
                        <small class="text-muted">
                            Insira seu salário líquido mensal (após descontos).
                        </small>
                    </div>

                    <!-- Botões -->
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-lg flex-grow-1">
                            <i class="bi bi-save me-2"></i>Atualizar Salário
                        </button>
                    </div>
                </form>

                <hr class="my-4">

                <!-- Informações Úteis -->
                <div class="alert alert-light">
                    <h6 class="mb-2"><i class="bi bi-lightbulb me-2"></i>Dicas</h6>
                    <ul class="mb-0">
                        <li>Atualize seu salário sempre que houver alterações</li>
                        <li>Use o valor líquido (já descontados impostos)</li>
                        <li>O saldo no dashboard é calculado como: Salário - Gastos do Mês - Dinheiro Guardado</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
