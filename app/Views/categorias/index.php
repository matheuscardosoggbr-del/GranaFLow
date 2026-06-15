<?php
$pageTitle = 'Categorias';
$pageKicker = 'Finance CRM';
$pageHeading = 'Categorias Personalizadas';
$activePage = 'categorias';
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

<div class="card formulario-card mb-4">
    <div class="card-body">
        <h5 class="card-title mb-3">Adicionar Categoria</h5>
        <form method="POST" action="<?= BASE_URL ?>categoria/salvar" class="row g-2">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            <div class="col-md-6">
                <input type="text" name="nome" class="form-control" placeholder="Nome da categoria" maxlength="30" required>
            </div>
            <div class="col-md-4">
                <select name="id_tipo" class="form-select" required>
                    <option value="2">Despesa</option>
                    <option value="1">Receita</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-acao w-100">
                    <i class="bi bi-plus-circle me-1"></i>Adicionar
                </button>
            </div>
        </form>
    </div>
</div>

<?php if (empty($categorias)): ?>
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>Você não tem categorias personalizadas. Use as categorias padrão.
    </div>
<?php else: ?>
    <div class="card formulario-card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover tabela-gastos mb-0">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Tipo</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categorias as $cat): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($cat['nome']) ?></strong></td>
                                <td>
                                    <span class="badge bg-<?= $cat['tipo_nome'] === 'Receita' ? 'success' : 'danger' ?>">
                                        <?= $cat['tipo_nome'] ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <form method="POST" action="<?= BASE_URL ?>categoria/deletar/<?= $cat['id_categoria'] ?>" style="display:inline;" onsubmit="return confirm('Deseja deletar esta categoria?');">
                                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger btn-acao">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php require_once dirname(__DIR__) . '/partials/shell_bottom.php'; ?>
