<?php
$pageTitle = 'Gastos';
$pageKicker = 'Finance CRM';
$pageHeading = 'Gerenciar Gastos';
$activePage = 'gastos';
require_once dirname(__DIR__) . '/partials/shell_top.php';
?>

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

<div class="d-flex justify-content-end mb-3">
    <a href="<?= BASE_URL ?>gastos/novo" class="btn btn-primary btn-acao">
        <i class="bi bi-plus-circle me-2"></i>Novo Gasto
    </a>
</div>

<div class="card formulario-card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-12">
                <label class="form-label">Busca rÃ¡pida</label>
                <input type="search" name="q" class="form-control" value="<?= htmlspecialchars($busca ?? '') ?>" placeholder="Pesquisar por descriÃ§Ã£o ou categoria">
            </div>
            <div class="col-md-4">
                <label class="form-label">Categoria</label>
                <select name="categoria" class="form-select">
                    <option value="">Todas as categorias</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?= $cat['id_categoria'] ?>" <?= $filtro_categoria == $cat['id_categoria'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">MÃªs</label>
                <input type="month" name="mes" class="form-control" value="<?= $filtro_mes ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Ordenar por</label>
                <select name="ordem" class="form-select">
                    <option value="data_desc" <?= $ordem == 'data_desc' ? 'selected' : '' ?>>Data (mais recente)</option>
                    <option value="data_asc" <?= $ordem == 'data_asc' ? 'selected' : '' ?>>Data (mais antiga)</option>
                    <option value="valor_desc" <?= $ordem == 'valor_desc' ? 'selected' : '' ?>>Valor (maior)</option>
                    <option value="valor_asc" <?= $ordem == 'valor_asc' ? 'selected' : '' ?>>Valor (menor)</option>
                    <option value="categoria" <?= $ordem == 'categoria' ? 'selected' : '' ?>>Categoria</option>
                </select>
            </div>
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-outline-primary btn-acao">
                    <i class="bi bi-search me-1"></i>Filtrar
                </button>
                <a href="<?= BASE_URL ?>gastos" class="btn btn-outline-secondary btn-acao">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>Limpar
                </a>
            </div>
        </form>
    </div>
</div>

<?php if (empty($gastos)): ?>
    <div class="alert alert-info text-center">
        <i class="bi bi-info-circle me-2"></i>Nenhum gasto encontrado.
    </div>
<?php else: ?>
    <div class="card formulario-card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover tabela-gastos mb-0">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>DescriÃ§Ã£o</th>
                            <th>Categoria</th>
                            <th class="text-end">Valor</th>
                            <th class="text-center">AÃ§Ãµes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($gastos as $gasto): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($gasto['data_gasto'])) ?></td>
                                <td><?= htmlspecialchars($gasto['descricao'] ?? 'â€”') ?></td>
                                <td>
                                    <span class="badge-categoria"><?= htmlspecialchars($gasto['categoria']) ?></span>
                                </td>
                                <td class="text-end">
                                    <span class="valor-gasto">
                                        <?= $gasto['simbolo'] ?> <?= number_format($gasto['valor'], 2, ',', '.') ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="<?= BASE_URL ?>gastos/editar/<?= $gasto['id_gasto'] ?>" class="btn btn-sm btn-outline-primary btn-acao" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="<?= BASE_URL ?>gastos/deletar/<?= $gasto['id_gasto'] ?>" style="display:inline;" onsubmit="return confirm('Deseja deletar este gasto?');">
                                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger btn-acao" title="Deletar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <hr>
            <div class="row g-2">
                <div class="col-md-6"><strong>Total de Gastos:</strong> <?= number_format($total, 2, ',', '.') ?></div>
                <div class="col-md-6 text-md-end"><strong>Quantidade:</strong> <?= count($gastos) ?> gasto(s)</div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php require_once dirname(__DIR__) . '/partials/shell_bottom.php'; ?>

