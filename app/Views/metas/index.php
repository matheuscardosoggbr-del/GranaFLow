<?php
$pageTitle = 'Metas';
$pageKicker = 'Finance CRM';
$pageHeading = 'Minhas Metas';
$activePage = 'metas';
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

<div class="d-flex justify-content-end mb-3">
    <a href="<?= BASE_URL ?>metas/novo" class="btn btn-primary btn-acao">
        <i class="bi bi-plus-circle me-2"></i>Nova Meta
    </a>
</div>

<?php if (empty($metas)): ?>
    <div class="alert alert-info text-center">
        <i class="bi bi-info-circle me-2"></i>Você ainda não criou nenhuma meta.
    </div>
<?php else: ?>
    <div class="row g-3">
        <?php foreach ($metas as $meta):
            $percentual = $meta['valor_limite'] > 0 ? ($meta['valor_guardado'] / $meta['valor_limite'] * 100) : 0;
            $cor_progresso = $percentual >= 100 ? 'success' : ($percentual >= 50 ? 'info' : 'warning');
            $tipo_badge = $meta['tipo'] === 'gasto' ? 'danger' : 'success';
        ?>
            <div class="col-md-6 col-lg-4">
                <div class="card card-resumo h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="card-title"><?= htmlspecialchars($meta['nome_meta']) ?></h6>
                            <span class="badge bg-<?= $tipo_badge ?>"><?= $meta['tipo'] === 'gasto' ? 'Controle' : 'Reserva' ?></span>
                        </div>
                        <div class="mb-3">
                            <div class="progress" style="height: 24px;">
                                <div class="progress-bar bg-<?= $cor_progresso ?>" role="progressbar" style="width: <?= min(100, $percentual) ?>%;">
                                    <?= number_format($percentual, 0) ?>%
                                </div>
                            </div>
                        </div>
                        <div class="row text-center mb-3">
                            <div class="col-6">
                                <small class="page-subtitle">Guardado</small>
                                <div class="fw-bold text-success">R$ <?= number_format($meta['valor_guardado'], 2, ',', '.') ?></div>
                            </div>
                            <div class="col-6">
                                <small class="page-subtitle">Meta</small>
                                <div class="fw-bold text-primary">R$ <?= number_format($meta['valor_limite'], 2, ',', '.') ?></div>
                            </div>
                        </div>
                        <?php if ($meta['tipo'] === 'reserva'): ?>
                            <form method="POST" action="<?= BASE_URL ?>metas/guardar/<?= $meta['id_meta'] ?>" class="mb-2">
                                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                <div class="input-group input-group-sm mb-2">
                                    <input type="number" name="valor" class="form-control" step="0.01" min="0.01" placeholder="Valor" required>
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="bi bi-plus"></i>
                                    </button>
                                </div>
                            </form>
                        <?php endif; ?>
                        <div class="d-flex gap-2">
                            <a href="<?= BASE_URL ?>metas/editar/<?= $meta['id_meta'] ?>" class="btn btn-sm btn-outline-primary flex-grow-1">
                                <i class="bi bi-pencil me-1"></i>Editar
                            </a>
                            <form method="POST" action="<?= BASE_URL ?>metas/deletar/<?= $meta['id_meta'] ?>" style="display:inline; flex-grow:1;" onsubmit="return confirm('Deseja deletar esta meta?');">
                                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                    <i class="bi bi-trash me-1"></i>Deletar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once dirname(__DIR__) . '/partials/shell_bottom.php'; ?>
