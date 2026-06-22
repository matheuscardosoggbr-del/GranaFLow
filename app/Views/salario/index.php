<?php
$pageTitle = 'SalÃ¡rio';
$pageKicker = 'Finance CRM';
$pageHeading = 'Meu SalÃ¡rio';
$activePage = 'salario';
require_once dirname(__DIR__) . '/partials/shell_top.php';
?>

<?php if (!empty($_SESSION['sucesso'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($_SESSION['sucesso']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['sucesso']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['erro'])): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-exclamation-circle me-2"></i><?= htmlspecialchars($_SESSION['erro']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['erro']); ?>
<?php endif; ?>

<div class="card formulario-card">
    <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>salario/salvar">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            <div class="alert alert-info mb-4">
                <div class="text-center">
                    <small class="page-subtitle">SalÃ¡rio Atual</small>
                    <h3 class="mb-0 text-primary">R$ <?= number_format($salario, 2, ',', '.') ?></h3>
                </div>
            </div>
            <div class="mb-3">
                <label for="valor" class="form-label">Novo SalÃ¡rio <span class="text-danger">*</span></label>
                <div class="input-group input-group-lg">
                    <span class="input-group-text">R$</span>
                    <input type="number" id="valor" name="valor" class="form-control" step="0.01" min="0.01" value="<?= number_format($salario, 2, '.', '') ?>" placeholder="0,00" required>
                </div>
                <small class="page-subtitle">Insira seu salÃ¡rio lÃ­quido mensal.</small>
            </div>
            <button type="submit" class="btn btn-primary btn-acao btn-lg w-100">
                <i class="bi bi-save me-2"></i>Atualizar SalÃ¡rio
            </button>
        </form>

        <hr class="my-4">

        <div class="alert alert-light">
            <h6 class="mb-2"><i class="bi bi-lightbulb me-2"></i>Dicas</h6>
            <ul class="mb-0">
                <li>Atualize seu salÃ¡rio sempre que houver alteraÃ§Ãµes</li>
                <li>Use o valor lÃ­quido jÃ¡ descontados impostos</li>
                <li>O saldo no dashboard Ã© calculado como: salÃ¡rio - gastos do mÃªs - dinheiro guardado</li>
            </ul>
        </div>

        <?php if (!empty($historico)): ?>
            <hr class="my-4">
            <h6 class="mb-3"><i class="bi bi-clock-history me-2"></i>HistÃ³rico recente</h6>
            <div class="table-responsive">
                <table class="table table-sm tabela-gastos mb-0">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th class="text-end">Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($historico as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['data_formatada'] ?? '') ?></td>
                                <td class="text-end">R$ <?= number_format((float)$item['valor'], 2, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/partials/shell_bottom.php'; ?>

