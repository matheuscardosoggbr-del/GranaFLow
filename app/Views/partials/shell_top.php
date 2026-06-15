<?php
$pageTitle = $pageTitle ?? 'GranaFlow';
$pageKicker = $pageKicker ?? 'Dashboard';
$pageHeading = $pageHeading ?? $pageTitle;
$activePage = $activePage ?? 'dashboard';
$userName = $nome_usuario ?? ($_SESSION['nome_usuario'] ?? 'Usuário');
$userEmail = $_SESSION['email'] ?? '';

$navItems = [
    'dashboard' => ['label' => 'Home page', 'icon' => 'house-door', 'href' => BASE_URL . 'dashboard'],
    'gastos' => ['label' => 'Gastos', 'icon' => 'receipt', 'href' => BASE_URL . 'gastos'],
    'metas' => ['label' => 'Metas', 'icon' => 'bullseye', 'href' => BASE_URL . 'metas'],
    'categorias' => ['label' => 'Categorias', 'icon' => 'tags', 'href' => BASE_URL . 'categorias'],
    'relatorios' => ['label' => 'Relatórios', 'icon' => 'bar-chart', 'href' => BASE_URL . 'relatorio'],
    'salario' => ['label' => 'Salário', 'icon' => 'cash-stack', 'href' => BASE_URL . 'salario'],
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> — GranaFlow</title>
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
                    <div class="crm-brand-sub">Finance CRM</div>
                </div>
            </div>

            <label class="crm-search">
                <i class="bi bi-search"></i>
                <input type="search" placeholder="Search..." aria-label="Search">
            </label>

            <div class="crm-sidebar-section">
                <div class="crm-sidebar-title">Workspace</div>
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
                    <label class="crm-top-search">
                        <i class="bi bi-search"></i>
                        <input type="search" placeholder="Search finance data..." aria-label="Search finance data" data-global-search>
                    </label>
                    <button class="crm-icon-btn" type="button" title="Notifications"><i class="bi bi-bell"></i></button>
                    <button class="crm-icon-btn" type="button" title="Settings"><i class="bi bi-gear"></i></button>
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
