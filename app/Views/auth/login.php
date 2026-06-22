<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar - GranaFlow</title>
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
                <h1>Controle financeiro com leitura clara e foco no que importa.</h1>
                <p>Uma experiencia moderna para acompanhar gastos, metas e salario com contraste forte, navegacao consistente e temas personalizaveis.</p>
            </div>
            <div class="auth-pill-row">
                <span class="auth-pill">Painel central</span>
                <span class="auth-pill">Temas visuais</span>
                <span class="auth-pill">Busca funcional</span>
            </div>
        </section>

        <section class="auth-panel">
            <div class="auth-card">
                <div class="auth-header">
                    <div class="auth-brand">
                        <div class="auth-brand-icon"><i class="bi bi-shield-lock"></i></div>
                        <span class="auth-brand-name">GranaFlow</span>
                    </div>
                    <h2>Entrar no sistema</h2>
                </div>

                <div class="auth-body">
                    <?php if (isset($sucesso)): ?>
                        <div class="alert alert-success mb-3">
                            <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($sucesso) ?>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($erro)): ?>
                        <div class="alert alert-danger mb-3">
                            <i class="bi bi-exclamation-circle me-2"></i><?= htmlspecialchars($erro) ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= BASE_URL ?>auth/login" method="POST" class="d-grid gap-3">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        <div>
                            <label class="form-label">E-mail</label>
                            <input type="email" name="email" class="form-control" placeholder="cliente@email.com" required autofocus>
                        </div>
                        <div>
                            <label class="form-label">Senha</label>
                            <input type="password" name="senha" class="form-control" placeholder="********" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Entrar</button>
                    </form>

                    <div class="auth-foot mt-4">
                        Não tem conta? <a href="<?= BASE_URL ?>auth/cadastro">Criar agora</a>
                    </div>
                </div>
            </div>
        </section>
    </div>
</body>
</html>



