<?php
$pageTitle = isset($receita) ? 'Editar Receita' : 'Nova Receita';
$pageKicker = 'Finance CRM';
$pageHeading = isset($receita) ? 'Editar Receita' : 'Nova Receita';
$activePage = 'receitas';
require_once dirname(__DIR__) . '/partials/shell_top.php';
?>

<div class="card formulario-card" style="max-width:680px;">
    <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>receitas/salvar">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            <input type="hidden" name="id_receita" value="<?= isset($receita) ? $receita['id_receita'] : 0 ?>">

            <div class="mb-3">
                <label for="descricao" class="form-label">DescriÃ§Ã£o <span class="text-danger">*</span></label>
                <input type="text" id="descricao" name="descricao" class="form-control"
                    value="<?= isset($receita) ? htmlspecialchars($receita['descricao']) : '' ?>"
                    placeholder="Ex: Freela, venda, salÃ¡rio extra" maxlength="255" required>
            </div>

            <div class="mb-3">
                <label for="valor" class="form-label">Valor <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">R$</span>
                    <input type="number" id="valor" name="valor" class="form-control" step="0.01" min="0.01"
                        value="<?= isset($receita) ? number_format($receita['valor'], 2, '.', '') : '' ?>"
                        placeholder="0,00" required>
                </div>
            </div>

            <div class="mb-4">
                <label for="data_receita" class="form-label">Data <span class="text-danger">*</span></label>
                <input type="date" id="data_receita" name="data_receita" class="form-control"
                    value="<?= isset($receita) ? $receita['data_receita'] : date('Y-m-d') ?>" required>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-acao">
                    <i class="bi bi-save me-2"></i>Salvar
                </button>
                <a href="<?= BASE_URL ?>receitas" class="btn btn-outline-secondary btn-acao">
                    <i class="bi bi-x-circle me-2"></i>Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/partials/shell_bottom.php'; ?>

