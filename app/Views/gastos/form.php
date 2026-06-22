<?php
$pageTitle = isset($gasto) ? 'Editar Gasto' : 'Novo Gasto';
$pageKicker = 'Finance CRM';
$pageHeading = isset($gasto) ? 'Editar Gasto' : 'Novo Gasto';
$activePage = 'gastos';
require_once dirname(__DIR__) . '/partials/shell_top.php';
?>

<div class="card formulario-card" style="max-width:680px;">
    <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>gastos/salvar">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            <input type="hidden" name="id_gasto" value="<?= isset($gasto) ? $gasto['id_gasto'] : 0 ?>">

            <div class="mb-3">
                <label for="categoria" class="form-label">Categoria <span class="text-danger">*</span></label>
                <select id="categoria" name="id_categoria" class="form-select" required>
                    <option value="">Selecione uma categoria</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?= $cat['id_categoria'] ?>" <?= isset($gasto) && $gasto['id_categoria'] == $cat['id_categoria'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="descricao" class="form-label">DescriÃ§Ã£o <span class="text-danger">*</span></label>
                <input type="text" id="descricao" name="descricao" class="form-control" value="<?= isset($gasto) ? htmlspecialchars($gasto['descricao']) : '' ?>" placeholder="Ex: Compra no mercado" maxlength="255" required>
            </div>

            <div class="mb-3">
                <label for="valor" class="form-label">Valor <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">R$</span>
                    <input type="number" id="valor" name="valor" class="form-control" step="0.01" min="0.01" value="<?= isset($gasto) ? number_format($gasto['valor'], 2, '.', '') : '' ?>" placeholder="0,00" required>
                </div>
            </div>

            <div class="mb-4">
                <label for="data_gasto" class="form-label">Data <span class="text-danger">*</span></label>
                <input type="date" id="data_gasto" name="data_gasto" class="form-control" value="<?= isset($gasto) ? $gasto['data_gasto'] : date('Y-m-d') ?>" required>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-acao">
                    <i class="bi bi-save me-2"></i>Salvar
                </button>
                <a href="<?= BASE_URL ?>gastos" class="btn btn-outline-secondary btn-acao">
                    <i class="bi bi-x-circle me-2"></i>Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/partials/shell_bottom.php'; ?>

