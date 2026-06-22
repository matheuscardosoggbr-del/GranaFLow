<?php
$pageTitle = 'Perfil';
$pageKicker = 'ConfiguraÃ§Ãµes';
$pageHeading = 'Meu Perfil';
$activePage = '';
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

<div class="row g-3">
    <div class="col-lg-5">
        <div class="card formulario-card h-100">
            <div class="card-header">
                <div class="card-header-icon" style="background:rgba(108,99,255,0.12);color:var(--accent);">
                    <i class="bi bi-person-badge"></i>
                </div>
                <h6>Dados da Conta</h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="crm-user-avatar" style="width:58px;height:58px;border-radius:18px;">
                        <?= strtoupper(substr($usuario['nome'] ?? ($_SESSION['nome'] ?? 'U'), 0, 1)) ?>
                    </div>
                    <div>
                        <div class="fw-bold" style="font-size:1.1rem;"><?= htmlspecialchars($usuario['nome'] ?? ($_SESSION['nome'] ?? 'UsuÃ¡rio')) ?></div>
                        <div class="page-subtitle"><?= htmlspecialchars($usuario['email'] ?? ($_SESSION['email'] ?? '')) ?></div>
                    </div>
                </div>

                <div class="mb-2">
                    <small class="page-subtitle">ID do usuÃ¡rio</small>
                    <div class="fw-semibold">#<?= (int) ($_SESSION['id_usuario'] ?? 0) ?></div>
                </div>

                <div class="mb-2">
                    <small class="page-subtitle">Conta criada em</small>
                    <div class="fw-semibold"><?= htmlspecialchars($usuario['data_criacao'] ?? 'â€”') ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card formulario-card mb-3">
            <div class="card-header">
                <div class="card-header-icon" style="background:rgba(52,211,153,0.12);color:var(--green);">
                    <i class="bi bi-person-gear"></i>
                </div>
                <h6>Editar Perfil</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= BASE_URL ?>perfil/atualizar" class="row g-3">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <div class="col-md-6">
                        <label class="form-label">Nome</label>
                        <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($usuario['nome'] ?? '') ?>" minlength="3" maxlength="30" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">E-mail</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($usuario['email'] ?? '') ?>" required>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-acao">
                            <i class="bi bi-check2 me-2"></i>Salvar alteraÃ§Ãµes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card formulario-card" id="senha">
            <div class="card-header">
                <div class="card-header-icon" style="background:rgba(248,113,113,0.10);color:var(--red);">
                    <i class="bi bi-shield-lock"></i>
                </div>
                <h6>Alterar Senha</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= BASE_URL ?>perfil/senha" class="row g-3">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <div class="col-12">
                        <label class="form-label">Senha atual</label>
                        <input type="password" name="senha_atual" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nova senha</label>
                        <input type="password" name="senha_nova" class="form-control" minlength="6" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Confirmar nova senha</label>
                        <input type="password" name="confirmar_senha" class="form-control" minlength="6" required>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-warning btn-acao">
                            <i class="bi bi-key me-2"></i>Atualizar senha
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/partials/shell_bottom.php'; ?>

