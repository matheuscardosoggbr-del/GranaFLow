<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($meta) ? 'Editar' : 'Nova' ?> Meta — GranaFlow</title>
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
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>metas">Voltar</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4" style="max-width:600px; padding-bottom:60px;">
        <h2 class="mb-4">
            <i class="bi bi-<?= isset($meta) ? 'pencil' : 'plus-circle' ?> me-2"></i>
            <?= isset($meta) ? 'Editar' : 'Nova' ?> Meta
        </h2>

        <div class="card card-resumo">
            <div class="card-body">
                <form method="POST" action="<?= BASE_URL ?>metas/salvar">
                    <!-- Token CSRF -->
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    
                    <!-- ID da meta (se editar) -->
                    <?php if (isset($meta)): ?>
                        <input type="hidden" name="id_meta" value="<?= $meta['id_meta'] ?>">
                    <?php else: ?>
                        <input type="hidden" name="id_meta" value="0">
                    <?php endif; ?>

                    <!-- Nome da Meta -->
                    <div class="mb-3">
                        <label for="nome_meta" class="form-label">Nome da Meta <span class="text-danger">*</span></label>
                        <input type="text" id="nome_meta" name="nome_meta" class="form-control" 
                               value="<?= isset($meta) ? htmlspecialchars($meta['nome_meta']) : '' ?>"
                               placeholder="Ex: Viagem para o Rio" maxlength="50" required>
                    </div>

                    <!-- Valor Limite -->
                    <div class="mb-3">
                        <label for="valor_limite" class="form-label">Valor <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <input type="number" id="valor_limite" name="valor_limite" class="form-control" step="0.01" min="0.01"
                                   value="<?= isset($meta) ? number_format($meta['valor_limite'], 2, '.', '') : '' ?>"
                                   placeholder="0,00" required>
                        </div>
                    </div>

                    <!-- Tipo de Meta -->
                    <div class="mb-3">
                        <label for="tipo" class="form-label">Tipo de Meta <span class="text-danger">*</span></label>
                        <select id="tipo" name="tipo" class="form-select" required>
                            <option value="gasto" <?= !isset($meta) || $meta['tipo'] === 'gasto' ? 'selected' : '' ?>>
                                Controle de Gasto (Limite de gastos)
                            </option>
                            <option value="reserva" <?= isset($meta) && $meta['tipo'] === 'reserva' ? 'selected' : '' ?>>
                                Reserva (Guardar dinheiro)
                            </option>
                        </select>
                        <small class="text-muted">
                            <strong>Controle:</strong> Define um limite de gastos em uma categoria.<br>
                            <strong>Reserva:</strong> Permite guardar dinheiro progressivamente para uma meta.
                        </small>
                    </div>

                    <!-- Botões -->
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Salvar
                        </button>
                        <a href="<?= BASE_URL ?>metas" class="btn btn-secondary">
                            <i class="bi bi-x-circle me-2"></i>Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
