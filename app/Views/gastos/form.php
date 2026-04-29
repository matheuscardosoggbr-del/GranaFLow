<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($gasto) ? 'Editar' : 'Novo' ?> Gasto — GranaFlow</title>
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
                        <a class="nav-link" href="<?= BASE_URL ?>gastos">Voltar</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4" style="max-width:600px; padding-bottom:60px;">
        <h2 class="mb-4">
            <i class="bi bi-<?= isset($gasto) ? 'pencil' : 'plus-circle' ?> me-2"></i>
            <?= isset($gasto) ? 'Editar' : 'Novo' ?> Gasto
        </h2>

        <div class="card card-resumo">
            <div class="card-body">
                <form method="POST" action="<?= BASE_URL ?>gastos/salvar">
                    <!-- Token CSRF -->
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    
                    <!-- ID do gasto (se editar) -->
                    <?php if (isset($gasto)): ?>
                        <input type="hidden" name="id_gasto" value="<?= $gasto['id_gasto'] ?>">
                    <?php else: ?>
                        <input type="hidden" name="id_gasto" value="0">
                    <?php endif; ?>

                    <!-- Categoria -->
                    <div class="mb-3">
                        <label for="categoria" class="form-label">Categoria <span class="text-danger">*</span></label>
                        <select id="categoria" name="id_categoria" class="form-select" required>
                            <option value="">Selecione uma categoria</option>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?= $cat['id_categoria'] ?>"
                                    <?= isset($gasto) && $gasto['id_categoria'] == $cat['id_categoria'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['nome']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Descrição -->
                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição <span class="text-danger">*</span></label>
                        <input type="text" id="descricao" name="descricao" class="form-control" 
                               value="<?= isset($gasto) ? htmlspecialchars($gasto['descricao']) : '' ?>"
                               placeholder="Ex: Compra no mercado" maxlength="255" required>
                    </div>

                    <!-- Valor -->
                    <div class="mb-3">
                        <label for="valor" class="form-label">Valor <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <input type="number" id="valor" name="valor" class="form-control" step="0.01" min="0.01"
                                   value="<?= isset($gasto) ? number_format($gasto['valor'], 2, '.', '') : '' ?>"
                                   placeholder="0,00" required>
                        </div>
                    </div>

                    <!-- Data -->
                    <div class="mb-3">
                        <label for="data_gasto" class="form-label">Data <span class="text-danger">*</span></label>
                        <input type="date" id="data_gasto" name="data_gasto" class="form-control"
                               value="<?= isset($gasto) ? $gasto['data_gasto'] : date('Y-m-d') ?>"
                               required>
                    </div>

                    <!-- Botões -->
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Salvar
                        </button>
                        <a href="<?= BASE_URL ?>gastos" class="btn btn-secondary">
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
