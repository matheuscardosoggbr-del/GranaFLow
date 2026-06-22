<?php
$pageTitle = $pageTitle ?? 'GranaFlow';
$pageKicker = $pageKicker ?? 'Painel';
$pageHeading = $pageHeading ?? $pageTitle;
$activePage = $activePage ?? 'dashboard';
$userName = $nome_usuario ?? ($_SESSION['nome_usuario'] ?? ($_SESSION['nome'] ?? 'Usuario'));
$userEmail = $_SESSION['email'] ?? '';

$navItems = [
    'dashboard' => ['label' => 'Painel', 'icon' => 'house-door', 'href' => BASE_URL . 'dashboard'],
    'gastos' => ['label' => 'Gastos', 'icon' => 'receipt', 'href' => BASE_URL . 'gastos'],
    'receitas' => ['label' => 'Receitas', 'icon' => 'cash-coin', 'href' => BASE_URL . 'receitas'],
    'metas' => ['label' => 'Metas', 'icon' => 'bullseye', 'href' => BASE_URL . 'metas'],
    'categorias' => ['label' => 'Categorias', 'icon' => 'tags', 'href' => BASE_URL . 'categorias'],
    'relatorios' => ['label' => 'Relatorios', 'icon' => 'bar-chart', 'href' => BASE_URL . 'relatorio'],
    'salario' => ['label' => 'Salario', 'icon' => 'cash-stack', 'href' => BASE_URL . 'salario'],
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - GranaFlow</title>
    <script src="<?= BASE_URL ?>js/theme.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/Style.css">
    <meta name="csrf-token" content="<?= htmlspecialchars($csrf_token ?? '') ?>">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= BASE_URL ?>js/search.js" defer></script>
</head>
<body data-base-url="<?= BASE_URL ?>" class="dashboard-shell shell-page">
    <div class="crm-shell">
        <aside class="crm-sidebar">
            <div class="crm-sidebar-brand">
                <div class="crm-brand-mark"><i class="bi bi-graph-up-arrow"></i></div>
                <div>
                    <div class="crm-brand-name">GranaFlow</div>
                    <div class="crm-brand-sub">Gestao Financeira</div>
                </div>
            </div>

            <label class="crm-search">
                <i class="bi bi-search"></i>
                <input type="search" placeholder="Buscar..." aria-label="Buscar">
            </label>

            <div class="crm-sidebar-section">
                <div class="crm-sidebar-title">Area de trabalho</div>
                <?php foreach ($navItems as $key => $item): ?>
                    <a class="crm-nav-item <?= $activePage === $key ? 'active' : '' ?>" href="<?= htmlspecialchars($item['href']) ?>">
                        <i class="bi bi-<?= htmlspecialchars($item['icon']) ?>"></i>
                        <span><?= htmlspecialchars($item['label']) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>

            <div class="crm-sidebar-footer">
                <a href="<?= BASE_URL ?>auth/logout" class="crm-logout"><i class="bi bi-box-arrow-left"></i><span>Sair</span></a>
            </div>
        </aside>

        <main class="crm-main">
            <header class="crm-topbar">
                <div>
                    <div class="crm-page-kicker"><?= htmlspecialchars($pageKicker) ?></div>
                    <h1 class="crm-page-title"><?= htmlspecialchars($pageHeading) ?></h1>
                </div>
                <div class="crm-topbar-actions">
                    <label class="crm-top-search crm-top-search-inline">
                        <span class="crm-search-icon"><i class="bi bi-search"></i></span>
                        <input type="search" placeholder="Buscar dados financeiros..." aria-label="Buscar dados financeiros" data-global-search>
                    </label>
                    <div class="position-relative">
                        <button class="crm-icon-btn" type="button" title="Notificacoes" data-crm-flyout="notifications" aria-expanded="false">
                            <i class="bi bi-bell"></i>
                        </button>
                        <div class="crm-flyout" data-flyout="notifications">
                            <h6>Notificacoes</h6>
                            <div class="crm-flyout-item">
                                <i class="bi bi-check2-circle mt-1"></i>
                                <div>
                                    <strong>Nenhuma pendencia</strong>
                                    <small>Seu painel esta atualizado.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="position-relative">
                        <button class="crm-icon-btn" type="button" title="Configuracoes" data-crm-flyout="settings" aria-expanded="false">
                            <i class="bi bi-gear"></i>
                        </button>
                        <div class="crm-flyout" data-flyout="settings">
                            <h6>Configuracoes</h6>
                            <a class="crm-flyout-item" href="<?= BASE_URL ?>perfil">
                                <i class="bi bi-person-badge mt-1"></i>
                                <div>
                                    <strong>Meu perfil</strong>
                                    <small>Editar nome e e-mail.</small>
                                </div>
                            </a>
                            <a class="crm-flyout-item" href="<?= BASE_URL ?>perfil#senha">
                                <i class="bi bi-shield-lock mt-1"></i>
                                <div>
                                    <strong>Senha</strong>
                                    <small>Atualizar acesso da conta.</small>
                                </div>
                            </a>
                            <a class="crm-flyout-item" href="<?= BASE_URL ?>salario">
                                <i class="bi bi-cash-stack mt-1"></i>
                                <div>
                                    <strong>Salario</strong>
                                    <small>Ajustar renda mensal.</small>
                                </div>
                            </a>
                            <a class="crm-flyout-item" href="<?= BASE_URL ?>categorias">
                                <i class="bi bi-tags mt-1"></i>
                                <div>
                                    <strong>Categorias</strong>
                                    <small>Organizar suas classes.</small>
                                </div>
                            </a>
                            <a class="crm-flyout-item" href="<?= BASE_URL ?>auth/logout">
                                <i class="bi bi-box-arrow-left mt-1"></i>
                                <div>
                                    <strong>Sair</strong>
                                    <small>Encerrar a sessao.</small>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="crm-user-chip">
                        <div class="crm-user-avatar"><?= strtoupper(substr($userName, 0, 1)) ?></div>
                        <div class="crm-user-meta">
                            <strong><?= htmlspecialchars($userName) ?></strong>
                            <span><?= htmlspecialchars($userEmail) ?></span>
                        </div>
                    </div>
                </div>
            </header>
            <div class="crm-page">


