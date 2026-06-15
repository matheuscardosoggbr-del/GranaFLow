<?php
$pageTitle = isset($meta) ? 'Editar Meta' : 'Nova Meta';
$pageKicker = 'Finance CRM';
$pageHeading = isset($meta) ? 'Editar Meta' : 'Nova Meta';
$activePage = 'metas';
require_once dirname(__DIR__) . '/partials/shell_top.php';
?>

<div class="card formulario-card" style="max-width:680px;">
    <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>metas/salvar">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            <input type="hidden" name="id_meta" value="<?= isset($meta) ? $meta['id_meta'] : 0 ?>">

            <div class="mb-3">
                <label for="nome_meta" class="form-label">Nome da Meta <span class="text-danger">*</span></label>
                <input type="text" id="nome_meta" name="nome_meta" class="form-control" value="<?= isset($meta) ? htmlspecialchars($meta['nome_meta']) : '' ?>" placeholder="Ex: Viagem para o Rio" maxlength="50" required>
            </div>

            <div class="mb-3">
                <label for="valor_limite" class="form-label">Valor <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">R$</span>
                    <input type="number" id="valor_limite" name="valor_limite" class="form-control" step="0.01" min="0.01" value="<?= isset($meta) ? number_format($meta['valor_limite'], 2, '.', '') : '' ?>" placeholder="0,00" required>
                </div>
            </div>

            <div class="mb-4">
                <label for="tipo" class="form-label">Tipo de Meta <span class="text-danger">*</span></label>
                <select id="tipo" name="tipo" class="form-select" required>
                    <option value="gasto" <?= !isset($meta) || $meta['tipo'] === 'gasto' ? 'selected' : '' ?>>Controle de Gasto (Limite de gastos)</option>
                    <option value="reserva" <?= isset($meta) && $meta['tipo'] === 'reserva' ? 'selected' : '' ?>>Reserva (Guardar dinheiro)</option>
                </select>
                <small class="page-subtitle d-block mt-2">
                    <strong>Controle:</strong> define um limite de gastos.
                    <strong>Reserva:</strong> permite guardar dinheiro progressivamente.
                </small>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-acao">
                    <i class="bi bi-save me-2"></i>Salvar
                </button>
                <a href="<?= BASE_URL ?>metas" class="btn btn-outline-secondary btn-acao">
                    <i class="bi bi-x-circle me-2"></i>Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/partials/shell_bottom.php'; ?>
