<?php
$pageTitle = isset($categoria) ? 'Editar Categoria' : 'Nova Categoria';
$pageKicker = 'Finance CRM';
$pageHeading = isset($categoria) ? 'Editar Categoria' : 'Nova Categoria';
$activePage = 'categorias';
require_once dirname(__DIR__) . '/partials/shell_top.php';
?>

<div class="card formulario-card" style="max-width:720px;">
    <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>categoria/salvar">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            <input type="hidden" name="id_categoria" value="<?= isset($categoria) ? $categoria['id_categoria'] : 0 ?>">

            <div class="mb-3">
                <label for="nome" class="form-label">Nome da Categoria <span class="text-danger">*</span></label>
                <input type="text" id="nome" name="nome" class="form-control"
                    value="<?= isset($categoria) ? htmlspecialchars($categoria['nome']) : '' ?>"
                    maxlength="30" placeholder="Ex: AlimentaÃ§Ã£o" required>
            </div>

            <div class="mb-3">
                <label for="id_tipo" class="form-label">Tipo <span class="text-danger">*</span></label>
                <select id="id_tipo" name="id_tipo" class="form-select" required>
                    <option value="2" <?= !isset($categoria) || (int)$categoria['id_tipo'] === 2 ? 'selected' : '' ?>>Despesa</option>
                    <option value="1" <?= isset($categoria) && (int)$categoria['id_tipo'] === 1 ? 'selected' : '' ?>>Receita</option>
                </select>
                <small class="page-subtitle d-block mt-2">
                    Categorias de despesa ajudam a organizar gastos. Categorias de receita organizam entradas.
                </small>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-acao">
                    <i class="bi bi-save me-2"></i>Salvar
                </button>
                <a href="<?= BASE_URL ?>categorias" class="btn btn-outline-secondary btn-acao">
                    <i class="bi bi-x-circle me-2"></i>Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/partials/shell_bottom.php'; ?>

