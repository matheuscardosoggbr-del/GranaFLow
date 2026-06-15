<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro — GranaFlow</title>
    <script src="<?= BASE_URL ?>js/theme.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/Style.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;800&display=swap" rel="stylesheet">
</head>
<body class="auth-page">
    <div class="auth-layout">
        <section class="auth-visual d-none d-md-flex">
            <div class="auth-brand-hero">
                <div class="auth-mark"><i class="bi bi-graph-up-arrow"></i></div>
                <span>GranaFlow</span>
            </div>
            <div class="auth-hero-copy">
                <h1>Uma conta para organizar sua vida financeira com mais clareza.</h1>
                <p>Cadastre-se e acompanhe gastos, metas e salário com uma interface limpa, responsiva e com temas bem contrastados.</p>
            </div>
            <div class="auth-pill-row">
                <span class="auth-pill">Controle de gastos</span>
                <span class="auth-pill">Metas e salário</span>
                <span class="auth-pill">Visual premium</span>
            </div>
        </section>

        <section class="auth-panel">
            <div class="auth-card">
                <div class="auth-header">
                    <div class="auth-brand">
                        <div class="auth-brand-icon"><i class="bi bi-person-plus"></i></div>
                        <span class="auth-brand-name">GranaFlow</span>
                    </div>
                    <h2>Criar conta gratuita</h2>
                </div>

                <div class="auth-body">
                    <?php if (isset($erro)): ?>
                        <div class="alert alert-danger mb-3">
                            <i class="bi bi-exclamation-circle me-2"></i><?= htmlspecialchars($erro) ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= BASE_URL ?>auth/cadastro" method="POST" class="d-grid gap-3">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        <div>
                            <label class="form-label">Nome completo</label>
                            <input type="text" name="nome" class="form-control" placeholder="Seu nome" minlength="3" maxlength="30" required autofocus>
                        </div>
                        <div>
                            <label class="form-label">E-mail</label>
                            <input type="email" name="email" class="form-control" placeholder="cliente@email.com" required>
                        </div>
                        <div>
                            <label class="form-label">Senha</label>
                            <input type="password" name="senha" class="form-control" placeholder="Min. 6 caracteres" minlength="6" required>
                        </div>
                        <div>
                            <label class="form-label">Confirmar senha</label>
                            <input type="password" name="confirmar_senha" class="form-control" placeholder="Confirme sua senha" minlength="6" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Criar conta</button>
                    </form>

                    <div class="auth-foot mt-4">
                        Já tem conta? <a href="<?= BASE_URL ?>auth/login">Fazer login</a>
                    </div>
                </div>
            </div>
        </section>
    </div>
</body>
</html>
