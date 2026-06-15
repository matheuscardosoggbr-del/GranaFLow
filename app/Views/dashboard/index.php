<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — GranaFlow</title>
    <script src="<?= BASE_URL ?>js/theme.js"></script>
    <script src="<?= BASE_URL ?>js/search.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/Style.css">
    <meta name="csrf-token" content="<?= $csrf_token ?? '' ?>">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        *, *::before, *::after { font-family: 'Roboto', sans-serif !important; }
        .dashboard-shell .navbar-custom { display: none !important; }
        .crm-shell {
            display: grid;
            grid-template-columns: 280px 1fr;
            min-height: 100vh;
            background: linear-gradient(180deg, #f2f4fb 0%, #edf1f8 100%);
            color: #1f2937;
        }
        .crm-sidebar {
            background: linear-gradient(180deg, #3a246f 0%, #24163e 100%);
            color: #fff;
            padding: 22px 18px;
            display: flex;
            flex-direction: column;
            gap: 18px;
            position: sticky;
            top: 0;
            height: 100vh;
            box-shadow: 10px 0 30px rgba(20, 12, 36, 0.18);
        }
        .crm-sidebar-brand, .crm-user-chip, .crm-search {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .crm-brand-mark, .crm-user-avatar, .crm-icon-btn {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            display: grid;
            place-items: center;
        }
        .crm-brand-mark {
            background: rgba(255,255,255,0.14);
            color: #fff;
            font-size: 1.15rem;
        }
        .crm-brand-name { font-size: 1.15rem; font-weight: 800; }
        .crm-brand-sub { font-size: 0.78rem; opacity: 0.72; margin-top: 2px; }
        .crm-search, .crm-top-search {
            background: rgba(255,255,255,0.10);
            border: 1px solid rgba(255,255,255,0.10);
            border-radius: 14px;
            padding: 0 14px;
            color: #fff;
        }
        .crm-search input, .crm-top-search input {
            background: transparent;
            border: 0;
            color: inherit;
            width: 100%;
            min-height: 44px;
            outline: none;
        }
        .crm-search input::placeholder, .crm-top-search input::placeholder { color: rgba(255,255,255,0.68); }
        .crm-sidebar-title {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: .12em;
            opacity: .65;
            margin: 0 0 8px;
        }
        .crm-nav-item, .crm-action-card, .crm-logout {
            display: flex;
            align-items: center;
            gap: 12px;
            border-radius: 14px;
            text-decoration: none;
            border: 0;
            width: 100%;
            padding: 12px 14px;
            color: rgba(255,255,255,0.92);
            background: transparent;
        }
        .crm-nav-item:hover, .crm-action-card:hover, .crm-logout:hover { background: rgba(255,255,255,0.10); color: #fff; }
        .crm-nav-item.active { background: rgba(255,255,255,0.16); box-shadow: inset 0 0 0 1px rgba(255,255,255,0.10); }
        .crm-sidebar-section { display: flex; flex-direction: column; gap: 8px; }
        .crm-action-card { text-align: left; }
        .crm-sidebar-footer { margin-top: auto; }
        .crm-main { padding: 18px 22px 30px; overflow: hidden; }
        .crm-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            background: rgba(255,255,255,0.85);
            border: 1px solid rgba(31,41,55,0.08);
            box-shadow: 0 10px 30px rgba(15,23,42,0.06);
            backdrop-filter: blur(14px);
            border-radius: 22px;
            padding: 18px 20px;
            margin-bottom: 18px;
        }
        .crm-page-kicker { font-size: .8rem; text-transform: uppercase; letter-spacing: .12em; color: #64748b; }
        .crm-page-title { margin: 2px 0 0; font-size: 1.6rem; font-weight: 800; color: #12213a; }
        .crm-topbar-actions { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; justify-content: flex-end; }
        .crm-top-search { min-width: 300px; background: #fff; color: #334155; }
        .crm-top-search input::placeholder { color: #94a3b8; }
        .crm-icon-btn {
            border: 1px solid rgba(31,41,55,0.08);
            background: #fff;
            color: #334155;
        }
        .crm-user-chip {
            background: #fff;
            border: 1px solid rgba(31,41,55,0.08);
            border-radius: 16px;
            padding: 8px 12px 8px 8px;
        }
        .crm-user-avatar { background: linear-gradient(135deg, #6d28d9, #2563eb); color: #fff; font-weight: 800; }
        .crm-user-meta { display: flex; flex-direction: column; line-height: 1.1; }
        .crm-user-meta strong { font-size: .92rem; color: #12213a; }
        .crm-user-meta span { font-size: .75rem; color: #64748b; }
        .crm-section-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 20px 0 12px;
        }
        .crm-section-title h3 { margin: 0; font-size: 1rem; font-weight: 800; color: #12213a; }
        .crm-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            padding: 8px 12px;
            font-size: .82rem;
            font-weight: 700;
            background: #fff;
            border: 1px solid rgba(31,41,55,0.08);
            color: #334155;
            box-shadow: 0 6px 18px rgba(15,23,42,0.06);
        }
        .crm-grid-quick {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
        }
        .crm-quick-card {
            border: 1px solid rgba(31,41,55,0.08);
            background: #fff;
            border-radius: 18px;
            padding: 18px;
            box-shadow: 0 10px 30px rgba(15,23,42,0.05);
            min-height: 120px;
        }
        .crm-quick-card .label { font-size: .78rem; color: #64748b; text-transform: uppercase; letter-spacing: .08em; }
        .crm-quick-card .value { font-size: 2rem; font-weight: 800; color: #0f172a; line-height: 1; margin-top: 10px; }
        .crm-grid-panels {
            display: grid;
            grid-template-columns: 2fr 1.1fr;
            gap: 18px;
        }
        .crm-panel {
            background: #fff;
            border: 1px solid rgba(31,41,55,0.08);
            border-radius: 22px;
            box-shadow: 0 12px 36px rgba(15,23,42,0.05);
            overflow: hidden;
        }
        .crm-panel .panel-head {
            padding: 18px 20px;
            border-bottom: 1px solid rgba(31,41,55,0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #12213a;
            font-weight: 800;
        }
        .crm-panel .panel-body { padding: 18px 20px; }
        .crm-panel .panel-body .card, .crm-panel .panel-body .card-resumo, .crm-panel .panel-body .formulario-card {
            background: #fff !important;
            border-color: rgba(31,41,55,0.08) !important;
        }
        .dashboard-shell .container.mt-4 {
            max-width: none !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
            margin: 0 !important;
            animation: none;
        }
        @media (max-width: 1200px) {
            .crm-shell { grid-template-columns: 92px 1fr; }
            .crm-sidebar .crm-search, .crm-sidebar .crm-nav-item span, .crm-sidebar .crm-action-card span, .crm-sidebar .crm-brand-sub, .crm-sidebar .crm-logout span { display:none; }
            .crm-top-search { min-width: 220px; }
            .crm-grid-quick { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .crm-grid-panels { grid-template-columns: 1fr; }
        }
        @media (max-width: 768px) {
            .crm-shell { grid-template-columns: 1fr; }
            .crm-sidebar { position: relative; height: auto; }
            .crm-topbar { flex-direction: column; align-items: stretch; }
            .crm-topbar-actions, .crm-top-search { width: 100%; }
            .crm-top-search { min-width: 0; }
            .crm-grid-quick { grid-template-columns: 1fr; }
        }
    </style>
    <style>
        .dashboard-shell .crm-shell,
        .dashboard-shell .crm-sidebar,
        .dashboard-shell .crm-topbar,
        .dashboard-shell .crm-quick-card,
        .dashboard-shell .crm-panel {
            background: var(--shell-bg) !important;
        }
        .dashboard-shell .crm-sidebar {
            background: var(--shell-sidebar) !important;
        }
        .dashboard-shell .crm-topbar,
        .dashboard-shell .crm-quick-card,
        .dashboard-shell .crm-panel,
        .dashboard-shell .crm-badge,
        .dashboard-shell .crm-user-chip,
        .dashboard-shell .crm-icon-btn,
        .dashboard-shell .crm-top-search {
            background: var(--shell-surface) !important;
            border-color: var(--shell-border) !important;
        }
        .dashboard-shell .crm-page-kicker,
        .dashboard-shell .crm-user-meta span,
        .dashboard-shell .crm-quick-card .label,
        .dashboard-shell .page-subtitle,
        .dashboard-shell .text-muted {
            color: var(--shell-text2) !important;
        }
        .dashboard-shell .crm-page-title,
        .dashboard-shell .crm-section-title h3,
        .dashboard-shell .crm-user-meta strong,
        .dashboard-shell .crm-quick-card .value,
        .dashboard-shell .crm-panel .panel-head {
            color: var(--shell-text) !important;
        }
        .dashboard-shell .crm-topbar-actions {
            min-width: 0;
        }
        .dashboard-shell .crm-top-search {
            min-width: 260px;
            width: min(100%, 300px);
        }
        .dashboard-shell .crm-page {
            padding-bottom: 24px;
        }
        @media (max-width: 1024px) {
            .dashboard-shell .crm-shell { grid-template-columns: 92px 1fr; }
            .dashboard-shell .crm-sidebar .crm-search,
            .dashboard-shell .crm-sidebar .crm-nav-item span,
            .dashboard-shell .crm-sidebar .crm-action-card span,
            .dashboard-shell .crm-sidebar .crm-brand-sub,
            .dashboard-shell .crm-sidebar .crm-logout span { display:none; }
        }
        @media (max-width: 768px) {
            .dashboard-shell .crm-shell { grid-template-columns: 1fr; }
            .dashboard-shell .crm-sidebar { position: relative; height: auto; }
            .dashboard-shell .crm-topbar { padding: 16px; }
            .dashboard-shell .crm-topbar-actions { flex-direction: column; align-items: stretch; }
            .dashboard-shell .crm-top-search,
            .dashboard-shell .crm-user-chip,
            .dashboard-shell .crm-icon-btn { width: 100%; }
            .dashboard-shell .crm-top-search { min-width: 0; }
            .dashboard-shell .crm-grid-quick { grid-template-columns: 1fr; }
        }
    </style>
</head>

<body data-base-url="<?= BASE_URL ?>" class="dashboard-shell shell-page">
    <div class="crm-shell">
        <aside class="crm-sidebar">
            <div class="crm-sidebar-brand">
                <div class="crm-brand-mark">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
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
                <a class="crm-nav-item active" href="<?= BASE_URL ?>dashboard"><i class="bi bi-house-door"></i><span>Home page</span></a>
                <a class="crm-nav-item" href="<?= BASE_URL ?>gastos"><i class="bi bi-receipt"></i><span>Gastos</span></a>
                <a class="crm-nav-item" href="<?= BASE_URL ?>metas"><i class="bi bi-bullseye"></i><span>Metas</span></a>
                <a class="crm-nav-item" href="<?= BASE_URL ?>categorias"><i class="bi bi-tags"></i><span>Categorias</span></a>
                <a class="crm-nav-item" href="<?= BASE_URL ?>relatorio"><i class="bi bi-bar-chart"></i><span>Relatórios</span></a>
                <a class="crm-nav-item" href="<?= BASE_URL ?>salario"><i class="bi bi-cash-stack"></i><span>Salário</span></a>
            </div>

            <div class="crm-sidebar-footer">
                <a href="<?= BASE_URL ?>auth/logout" class="crm-logout"><i class="bi bi-box-arrow-left"></i><span>Sair</span></a>
            </div>
        </aside>

        <main class="crm-main">
            <header class="crm-topbar">
                <div>
                    <div class="crm-page-kicker">Dashboard</div>
                    <h1 class="crm-page-title">Overview</h1>
                </div>
                <div class="crm-topbar-actions">
                    <label class="crm-top-search">
                        <i class="bi bi-search"></i>
                        <input type="search" placeholder="Search finance data..." aria-label="Search finance data" data-global-search>
                    </label>
                    <button class="crm-icon-btn" type="button" title="Notifications"><i class="bi bi-bell"></i></button>
                    <button class="crm-icon-btn" type="button" title="Settings"><i class="bi bi-gear"></i></button>
                    <div class="crm-user-chip">
                        <div class="crm-user-avatar"><?= strtoupper(substr($nome_usuario, 0, 1)) ?></div>
                        <div class="crm-user-meta">
                            <strong><?= htmlspecialchars($nome_usuario) ?></strong>
                            <span><?= htmlspecialchars($_SESSION['email'] ?? '') ?></span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
                <div class="container-fluid">
                    <a class="navbar-brand navbar-brand-new" href="#">
                        <div class="logo ">
                            <svg width="45" height="45" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg">
  <defs>
    <!-- Gradiente do fundo azul -->
    <linearGradient id="gradFundoAzul" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" style="stop-color:#4a76d4;stop-opacity:1" />
      <stop offset="100%" style="stop-color:#2a4a9e;stop-opacity:1" />
    </linearGradient>

    <!-- Gradiente da moeda azul -->
    <radialGradient id="gradMoeda" cx="50%" cy="40%" r="50%" fx="50%" fy="40%">
      <stop offset="0%" style="stop-color:#3d6eff;stop-opacity:1" />
      <stop offset="100%" style="stop-color:#1a3a8e;stop-opacity:1" />
    </radialGradient>

    <!-- Gradiente das barras -->
    <linearGradient id="gradBarra" x1="0%" y1="0%" x2="0%" y2="100%">
      <stop offset="0%" style="stop-color:#ffffff;stop-opacity:1" />
      <stop offset="100%" style="stop-color:#d1d5db;stop-opacity:1" />
    </linearGradient>

    <!-- Sombra projetada -->
    <filter id="sombraObjeto" x="-20%" y="-20%" width="140%" height="140%">
      <feGaussianBlur in="SourceAlpha" stdDeviation="8" />
      <feOffset dx="10" dy="10" result="offsetblur" />
      <feComponentTransfer>
        <feFuncA type="linear" slope="0.6" />
      </feComponentTransfer>
      <feMerge>
        <feMergeNode />
        <feMergeNode in="SourceGraphic" />
      </feMerge>
    </filter>

    <!-- Máscara do círculo interno -->
    <clipPath id="clipC">
      <circle cx="512" cy="512" r="430" />
    </clipPath>
  </defs>

  <!-- Fundo Preto -->

  <!-- Anel Externo -->
  <circle cx="512" cy="512" r="460" fill="#222" stroke="#444" stroke-width="2" />
  <circle cx="512" cy="512" r="445" fill="none" stroke="#000" stroke-width="15" />

  <!-- Conteúdo Interno -->
  <g clip-path="url(#clipC)">
    <!-- Base Cinza Claro -->
    <circle cx="512" cy="512" r="430" fill="#e5e7eb" />

    <!-- Área Azul Diagonal Esquerda -->
    <path d="M 0,0 L 550,0 L 250,1024 L 0,1024 Z" fill="url(#gradFundoAzul)" />
    
    <!-- Linha Divisória Preta -->
    <path d="M 550,0 L 250,1024" stroke="black" stroke-width="40" />

    <!-- Barras Brancas do Gráfico (Lado Direito) -->
    <g filter="url(#sombraObjeto)">
      <rect x="520" y="680" width="85" height="250" fill="url(#gradBarra)" rx="4" />
      <rect x="630" y="600" width="85" height="330" fill="url(#gradBarra)" rx="4" />
      <rect x="740" y="520" width="85" height="410" fill="url(#gradBarra)" rx="4" />
      <rect x="850" y="400" width="85" height="530" fill="url(#gradBarra)" rx="4" />
    </g>

    <!-- Seta Branca Diagonal Ajustada (Mais fina e longa) -->
    <g filter="url(#sombraObjeto)">
      <!-- Corpo da Seta (Reduzido de 90 para 65 de largura e estendido) -->
      <line x1="120" y1="940" x2="720" y2="340" stroke="#f3f4f6" stroke-width="65" stroke-linecap="butt" />
      
      <!-- Cabeça da Seta (Ponta de Flecha Simétrica alinhada ao novo traço) -->
      <g transform="translate(740, 320) rotate(-45)">
        <path d="M 80,0 L -80,-80 L -30,0 L -80,80 Z" fill="white" stroke="#333" stroke-width="4" stroke-linejoin="round" />
      </g>
    </g>

    <!-- Moeda Azul Centralizada -->
    <g filter="url(#sombraObjeto)">
      <circle cx="430" cy="450" r="160" fill="url(#gradMoeda)" stroke="#1a3a8e" stroke-width="4" />
      <circle cx="430" cy="450" r="145" fill="none" stroke="rgba(255,255,255,0.2)" stroke-width="2" />
      <!-- Símbolo do Dólar -->
      <text x="430" y="530" font-family="Arial, sans-serif" font-size="220" font-weight="bold" fill="white" text-anchor="middle">$</text>
    </g>
  </g>
</svg>
            </div>
                <span class="navbar-brand-text">GranaFlow</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center gap-1">
                    <li class="nav-item">
                        <div class="user-pill">
                            <div class="user-avatar"><?= strtoupper(substr($nome_usuario, 0, 1)) ?></div>
                            <span><?= htmlspecialchars($nome_usuario) ?></span>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>auth/logout">
                            <i class="bi bi-box-arrow-right"></i> Sair
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4" style="max-width:1200px; padding-bottom:60px;">

        <!-- ═══ CARDS DE RESUMO ═══ -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card card-resumo h-100" data-card="saldo">
                    <div class="card-body">
                        <div class="card-resumo-icon" style="background:rgba(124,106,247,0.12);color:var(--accent);">
                            <i class="bi bi-wallet2"></i>
                        </div>
                        <h6>Saldo</h6>
                        <div class="valor <?= $saldo >= 0 ? 'saldo-positivo' : 'saldo-negativo' ?>">
                            R$ <?= number_format($saldo, 2, ',', '.') ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card card-resumo h-100" data-card="total-mes">
                    <div class="card-body">
                        <div class="card-resumo-icon" style="background:rgba(248,113,113,0.1);color:var(--red);">
                            <i class="bi bi-calendar3"></i>
                        </div>
                        <h6>Gasto Mês</h6>
                        <div class="valor">R$ <?= number_format($total_mes, 2, ',', '.') ?></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card card-resumo h-100" data-card="total-geral">
                    <div class="card-body">
                        <div class="card-resumo-icon" style="background:rgba(251,191,36,0.1);color:var(--yellow);">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <h6>Gasto Total</h6>
                        <div class="valor">R$ <?= number_format($total_geral, 2, ',', '.') ?></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card card-resumo h-100" data-card="salario">
                    <div class="card-body">
                        <div class="card-resumo-icon" style="background:rgba(52,211,153,0.1);color:var(--green);">
                            <i class="bi bi-cash-stack"></i>
                        </div>
                        <h6>Salário</h6>
                        <div class="valor saldo-positivo">R$ <?= number_format($salario, 2, ',', '.') ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ═══ GRÁFICOS ═══ -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="grafico-container" style="height:100%;">
                    <h6><i class="bi bi-bar-chart-line me-2" style="color:var(--accent);opacity:.7;"></i>Saldo Mensal</h6>
                    <canvas id="graficoSaldo"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="grafico-container" style="height:100%;">
                    <h6><i class="bi bi-bullseye me-2" style="color:var(--accent);opacity:.7;"></i>Progresso das Metas</h6>
                    <canvas id="graficoMetas"></canvas>
                </div>
            </div>
        </div>

        <!-- ═══ FORMULÁRIOS — LINHA 1 ═══ -->
        <div class="row g-3 mb-4">
            <!-- Salário -->
            <div class="col-md-6">
                <div class="card formulario-card h-100">
                    <div class="card-header">
                        <div class="card-header-icon" style="background:rgba(52,211,153,0.12);color:var(--green);">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                        <h6>Atualizar Salário</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="<?= BASE_URL ?>dashboard/salvarSalario" class="form-ajax" id="form-salario">
                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                            <div class="mb-3">
                                <label class="form-label">Valor do Salário</label>
                                <input type="number" step="0.01" class="form-control" name="salario"
                                    value="<?= $salario ?>" required placeholder="0,00">
                            </div>
                            <button type="submit" class="btn btn-success btn-acao w-100">
                                <i class="bi bi-check2 me-1"></i> Salvar Salário
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Dinheiro Guardado -->
            <div class="col-md-6">
                <div class="card formulario-card h-100">
                    <div class="card-header">
                        <div class="card-header-icon" style="background:rgba(52,211,153,0.12);color:var(--green);">
                            <i class="bi bi-piggy-bank"></i>
                        </div>
                        <h6>Guardar Dinheiro <span style="font-size:12px;font-weight:400;color:var(--muted);">— subtraído do saldo</span></h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3 p-3 rounded" style="background:rgba(52,211,153,0.07);border:1px solid rgba(52,211,153,0.2);">
                            <div style="font-size:12px;color:var(--muted);margin-bottom:4px;">Total guardado</div>
                            <div data-type="guardado" style="font-size:1.4rem;font-weight:700;color:var(--green);">
                                <span class="valor">R$ <?= number_format($total_guardado, 2, ',', '.') ?></span>
                            </div>
                        </div>
                        <form method="POST" action="<?= BASE_URL ?>dashboard/guardarAvulso" class="form-ajax" id="form-guardado">
                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                            <div class="mb-3">
                                <label class="form-label">Selecione uma Meta</label>
                                <select class="form-select" name="id_meta">
                                    <option value="">Guardar sem destino</option>
                                    <?php foreach ($metas as $meta): ?>
                                        <option value="<?= $meta['id_meta'] ?>">
                                            <?= htmlspecialchars($meta['nome_meta']) ?> 
                                            (R$ <?= number_format($meta['valor_guardado'], 2, ',', '.') ?> / <?= number_format($meta['valor_limite'], 2, ',', '.') ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Descrição</label>
                                <input type="text" class="form-control" name="descricao" placeholder="Ex: Reserva de emergência">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Valor a Guardar</label>
                                <input type="number" step="0.01" min="0.01" class="form-control" name="valor"
                                    placeholder="0,00" required>
                            </div>
                            <button type="submit" class="btn btn-success btn-acao w-100">
                                <i class="bi bi-piggy-bank me-1"></i> Guardar Dinheiro
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- ═══ FORMULÁRIOS — LINHA 2 ═══ -->
        <div class="row g-3 mb-4">
            <!-- Adicionar Gasto -->
            <div class="col-md-6">
                <div class="card formulario-card h-100">
                    <div class="card-header">
                        <div class="card-header-icon" style="background:rgba(248,113,113,0.1);color:var(--red);">
                            <i class="bi bi-receipt"></i>
                        </div>
                        <h6>Adicionar Gasto</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="<?= BASE_URL ?>gastos/adicionar" class="form-ajax" id="form-gasto">
                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                            <div class="mb-3">
                                <label class="form-label">Categoria</label>
                                <select class="form-select" name="id_categoria" required>
                                    <option value="">Selecione...</option>
                                    <?php foreach ($categorias as $cat): ?>
                                        <option value="<?= $cat['id_categoria'] ?>"><?= htmlspecialchars($cat['nome']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Descrição</label>
                                <input type="text" class="form-control" name="descricao" placeholder="Descrição do gasto">
                            </div>
                            <div class="row g-2">
                                <div class="col-6 mb-3">
                                    <label class="form-label">Valor</label>
                                    <input type="number" step="0.01" class="form-control" name="valor" placeholder="0,00" required>
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label">Data</label>
                                    <input type="date" class="form-control" name="data_gasto" value="<?= date('Y-m-d') ?>" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-acao w-100">
                                <i class="bi bi-plus-circle me-1"></i> Adicionar Gasto
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Gastos Recorrentes -->
            <div class="col-md-6">
                <div class="card formulario-card h-100">
                    <div class="card-header">
                        <div class="card-header-icon" style="background:rgba(251,191,36,0.1);color:var(--yellow);">
                            <i class="bi bi-arrow-repeat"></i>
                        </div>
                        <h6>Cadastrar Gasto Recorrente</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="<?= BASE_URL ?>dashboard/adicionarRecorrente" class="form-ajax" id="form-recorrente">
                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                            <div class="mb-3">
                                <label class="form-label">Categoria</label>
                                <select class="form-select" name="id_categoria" required>
                                    <option value="">Selecione...</option>
                                    <?php foreach ($categorias as $cat): ?>
                                        <option value="<?= $cat['id_categoria'] ?>"><?= htmlspecialchars($cat['nome']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Descrição</label>
                                <input type="text" class="form-control" name="descricao" placeholder="Ex: Aluguel, Netflix" required>
                            </div>
                            <div class="row g-2">
                                <div class="col-6 mb-3">
                                    <label class="form-label">Valor</label>
                                    <input type="number" step="0.01" class="form-control" name="valor" placeholder="0,00" required>
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label">Dia Venc.</label>
                                    <input type="number" class="form-control" name="dia_vencimento" min="1" max="31" placeholder="1–31" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tipo</label>
                                <select class="form-select" id="tipo_rec" name="tipo" required>
                                    <option value="mensal">Mensal</option>
                                    <option value="parcelado">Parcelado</option>
                                </select>
                            </div>
                            <div class="mb-3" id="quantidade-container" style="display:none;">
                                <label class="form-label">Quantidade de Meses</label>
                                <input type="number" class="form-control" name="quantidade" min="1" placeholder="Ex: 12">
                            </div>
                            <button type="submit" class="btn btn-warning btn-acao w-100">
                                <i class="bi bi-arrow-repeat me-1"></i> Cadastrar Recorrente
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- ═══ FORMULÁRIOS — LINHA 3: Meta ═══ -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card formulario-card h-100">
                    <div class="card-header">
                        <div class="card-header-icon" style="background:rgba(124,106,247,0.12);color:var(--accent);">
                            <i class="bi bi-flag"></i>
                        </div>
                        <h6>Cadastrar Meta</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="<?= BASE_URL ?>metas/adicionar" class="form-ajax" id="form-meta">
                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                            <div class="mb-3">
                                <label class="form-label">Nome da Meta</label>
                                <input type="text" class="form-control" name="nome_meta" placeholder="Ex: Viagem, Reserva de emergência" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Valor Limite</label>
                                <input type="number" step="0.01" class="form-control" name="valor_limite" placeholder="0,00" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-acao w-100">
                                <i class="bi bi-plus-circle me-1"></i> Adicionar Meta
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- ═══ TABELA: GASTOS RECENTES ═══ -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card formulario-card">
                    <div class="card-header">
                        <div class="card-header-icon" style="background:rgba(124,106,247,0.12);color:var(--accent);">
                            <i class="bi bi-list-ul"></i>
                        </div>
                        <h6>Gastos Recentes</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover tabela-gastos mb-0">
                                <thead>
                                    <tr>
                                        <th>Data</th>
                                        <th>Categoria</th>
                                        <th>Descrição</th>
                                        <th>Valor</th>
                                        <th style="width:60px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($gastos)): ?>
                                        <tr><td colspan="5">
                                            <div class="empty-state">
                                                <i class="bi bi-inbox"></i>
                                                <p>Nenhum gasto registrado ainda</p>
                                            </div>
                                        </td></tr>
                                    <?php else: ?>
                                        <?php foreach (array_slice($gastos, 0, 10) as $gasto): ?>
                                            <tr>
                                                <td><span style="color:var(--muted);font-size:12px;">
                                                    <i class="bi bi-calendar3 me-1"></i>
                                                    <?= date('d/m/Y', strtotime($gasto['data_gasto'])) ?>
                                                </span></td>
                                                <td><span class="badge badge-categoria"><?= htmlspecialchars($gasto['categoria']) ?></span></td>
                                                <td style="color:var(--text2);"><?= htmlspecialchars($gasto['descricao'] ?? '—') ?></td>
                                                <td><span class="valor-gasto"><?= $gasto['simbolo'] ?> <?= number_format($gasto['valor'], 2, ',', '.') ?></span></td>
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <button class="btn btn-outline-primary btn-edit-ajax"
                                                            data-url="<?= BASE_URL ?>gastos/editar/<?= $gasto['id_gasto'] ?>"
                                                            data-id="<?= $gasto['id_gasto'] ?>"
                                                            data-type="gasto"
                                                            data-descricao="<?= htmlspecialchars($gasto['descricao'] ?? '') ?>"
                                                            data-valor="<?= $gasto['valor'] ?>"
                                                            data-data="<?= $gasto['data_gasto'] ?>"
                                                            data-categoria="<?= $gasto['id_categoria'] ?? '' ?>"
                                                            title="Editar">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <button class="btn btn-outline-danger btn-delete-ajax"
                                                            data-url="<?= BASE_URL ?>gastos/deletar/<?= $gasto['id_gasto'] ?>"
                                                            data-id="<?= $gasto['id_gasto'] ?>"
                                                            data-type="gasto"
                                                            title="Deletar">
                                                            <i class="bi bi-trash3"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ═══ TABELA: GASTOS RECORRENTES RECENTES ═══ -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card formulario-card">
                    <div class="card-header">
                        <div class="card-header-icon" style="background:rgba(251,191,36,0.1);color:var(--yellow);">
                            <i class="bi bi-arrow-repeat"></i>
                        </div>
                        <h6>Gastos Recorrentes Ativos</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover tabela-gastos tabela-recorrentes mb-0">
                                <thead>
                                    <tr>
                                        <th>Dia Venc.</th>
                                        <th>Categoria</th>
                                        <th>Descrição</th>
                                        <th>Tipo</th>
                                        <th>Valor</th>
                                        <th>Última Geração</th>
                                        <th style="width:80px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($recorrentes)): ?>
                                        <tr><td colspan="6">
                                            <div class="empty-state">
                                                <i class="bi bi-arrow-repeat"></i>
                                                <p>Nenhum gasto recorrente ativo</p>
                                            </div>
                                        </td></tr>
                                    <?php else: ?>
                                        <?php foreach ($recorrentes as $rec): ?>
                                            <tr>
                                                <td><span style="color:var(--muted);font-size:12px;">
                                                    <i class="bi bi-calendar3 me-1"></i>Dia <?= $rec['dia_vencimento'] ?>
                                                </span></td>
                                                <td><span class="badge badge-categoria"><?= htmlspecialchars($rec['categoria']) ?></span></td>
                                                <td style="color:var(--text2);"><?= htmlspecialchars($rec['descricao']) ?></td>
                                                <td>
                                                    <?php if ($rec['tipo'] === 'parcelado'): ?>
                                                        <span class="badge" style="background:rgba(248,113,113,0.15);color:var(--red);font-size:11px;">
                                                            Parcelado · <?= $rec['quantidade_meses'] ?>x restantes
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge" style="background:rgba(251,191,36,0.15);color:var(--yellow);font-size:11px;">
                                                            Mensal
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><span class="valor-gasto">R$ <?= number_format($rec['valor'], 2, ',', '.') ?></span></td>
                                                <td><span style="color:var(--muted);font-size:12px;">
                                                    <?= $rec['ultima_execucao'] ? date('d/m/Y', strtotime($rec['ultima_execucao'])) : '—' ?>
                                                </span></td>
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <button class="btn btn-outline-primary btn-edit-ajax"
                                                            data-url="<?= BASE_URL ?>dashboard/editarRecorrente/<?= $rec['id'] ?>"
                                                            data-id="<?= $rec['id'] ?>"
                                                            data-type="recorrente"
                                                            data-descricao="<?= htmlspecialchars($rec['descricao']) ?>"
                                                            data-valor="<?= $rec['valor'] ?>"
                                                            data-dia="<?= $rec['dia_vencimento'] ?>"
                                                            title="Editar">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <button class="btn btn-outline-danger btn-delete-ajax"
                                                            data-url="<?= BASE_URL ?>dashboard/deletarRecorrente/<?= $rec['id'] ?>"
                                                            data-id="<?= $rec['id'] ?>"
                                                            data-type="recorrente"
                                                            title="Deletar">
                                                            <i class="bi bi-trash3"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ═══ DINHEIRO GUARDADO — HISTÓRICO ═══ -->
        <?php if (!empty($historico_guardado)): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="card formulario-card">
                    <div class="card-header">
                        <div class="card-header-icon" style="background:rgba(52,211,153,0.12);color:var(--green);">
                            <i class="bi bi-piggy-bank"></i>
                        </div>
                        <h6>Últimos Registros — Dinheiro Guardado</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover tabela-gastos mb-0">
                                <thead>
                                    <tr>
                                        <th>Data</th>
                                        <th>Descrição</th>
                                        <th>Valor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($historico_guardado as $h): ?>
                                        <tr>
                                            <td><span style="color:var(--muted);font-size:12px;">
                                                <i class="bi bi-calendar3 me-1"></i>
                                                <?= date('d/m/Y', strtotime($h['data_registro'])) ?>
                                            </span></td>
                                            <td style="color:var(--text2);"><?= htmlspecialchars($h['descricao']) ?></td>
                                            <td><span style="color:var(--green);font-weight:600;">
                                                R$ <?= number_format($h['valor'], 2, ',', '.') ?>
                                            </span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- ═══ METAS ═══ -->
        <div class="row">
            <div class="col-12">
                <div class="card formulario-card">
                    <div class="card-header">
                        <div class="card-header-icon" style="background:rgba(52,211,153,0.1);color:var(--green);">
                            <i class="bi bi-trophy"></i>
                        </div>
                        <h6>Minhas Metas</h6>
                    </div>
                    <div class="card-body">
                        <div id="metas-list">
                        <?php if (empty($metas)): ?>
                            <div class="empty-state">
                                <i class="bi bi-flag"></i>
                                <p>Nenhuma meta cadastrada ainda</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($metas as $meta): ?>
                                <?php
                                $percentual = ($meta['valor_limite'] > 0)
                                    ? min(($meta['valor_guardado'] / $meta['valor_limite']) * 100, 100)
                                    : 0;
                                $isComplete = $percentual >= 100;
                                ?>
                                <div class="meta-item">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <div>
                                            <h6 class="mb-1">
                                                <?php if ($isComplete): ?>
                                                    <i class="bi bi-check-circle-fill me-1" style="color:var(--green);font-size:13px;"></i>
                                                <?php endif; ?>
                                                <?= htmlspecialchars($meta['nome_meta']) ?>
                                            </h6>
                                            <small class="text-muted">
                                                R$ <?= number_format($meta['valor_guardado'], 2, ',', '.') ?>
                                                <span style="color:var(--muted2);">de</span>
                                                R$ <?= number_format($meta['valor_limite'], 2, ',', '.') ?>
                                            </small>
                                        </div>
                                        <div class="d-flex gap-2 align-items-center">
                                            <!-- Botão com valor customizável -->
                                            <form method="POST" action="<?= BASE_URL ?>dashboard/guardarDinheiro"
                                                  class="d-flex gap-1 align-items-center" style="display:inline;">
                                                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                                <input type="hidden" name="id_meta" value="<?= $meta['id_meta'] ?>">
                                                <input type="number" step="0.01" min="0.01" name="valor"
                                                    class="form-control form-control-sm"
                                                    style="width:90px;" placeholder="R$ valor" required>
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    <i class="bi bi-plus-circle"></i>
                                                </button>
                                            </form>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button class="btn btn-outline-primary btn-edit-ajax"
                                                    data-url="<?= BASE_URL ?>metas/editar/<?= $meta['id_meta'] ?>"
                                                    data-id="<?= $meta['id_meta'] ?>"
                                                    data-type="meta"
                                                    data-nome="<?= htmlspecialchars($meta['nome_meta']) ?>"
                                                    data-limite="<?= $meta['valor_limite'] ?>"
                                                    title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-outline-danger btn-delete-ajax"
                                                    data-url="<?= BASE_URL ?>metas/deletar/<?= $meta['id_meta'] ?>"
                                                    data-id="<?= $meta['id_meta'] ?>"
                                                    data-type="meta"
                                                    title="Deletar">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="progress-meta">
                                        <div class="progress-bar" role="progressbar" style="width:<?= $percentual ?>%"></div>
                                    </div>
                                    <small style="color:<?= $isComplete ? 'var(--green)' : 'var(--muted)' ?>;font-size:11px;font-weight:500;">
                                        <?php if ($isComplete): ?>
                                            <i class="bi bi-star-fill me-1"></i> Meta concluída!
                                        <?php else: ?>
                                            <?= round($percentual, 1) ?>% concluído &middot; faltam R$ <?= number_format($meta['valor_limite'] - $meta['valor_guardado'], 2, ',', '.') ?>
                                        <?php endif; ?>
                                    </small>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </div><!-- /metas-list -->
                    </div>
                </div>
            </div>
        </div>

    </div><!-- /container -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.getElementById('tipo_rec').addEventListener('change', function () {
        const container = document.getElementById('quantidade-container');
        const input     = document.getElementById('quantidade-container').querySelector('input');
        if (this.value === 'parcelado') {
            container.style.display = 'block';
            input.required = true;
        } else {
            container.style.display = 'none';
            input.required = false;
        }
    });

    function getCSSVar(name) {
        return getComputedStyle(document.documentElement).getPropertyValue(name).trim();
    }

    function buildCharts() {
        const accent  = getCSSVar('--accent')  || '#7c6af7';
        const red     = getCSSVar('--red')     || '#f87171';
        const muted   = getCSSVar('--muted')   || '#7a82a0';
        const border  = getCSSVar('--border2') || 'rgba(255,255,255,0.11)';

        const scalesOpts = {
            x: { ticks: { color: muted, font: { family: 'Roboto', size: 11 } }, grid: { color: border } },
            y: { ticks: { color: muted, font: { family: 'Roboto', size: 11 }, callback: v => 'R$ ' + v.toLocaleString('pt-BR') }, grid: { color: border } }
        };

        const ctxSaldo = document.getElementById('graficoSaldo').getContext('2d');
        window._chartSaldo = new Chart(ctxSaldo, {
            type: 'line',
            data: {
                labels: <?= json_encode($grafico_saldo['labels'] ?? []) ?>,
                datasets: [{
                    label: 'Saldo',
                    data: <?= json_encode($grafico_saldo['valores'] ?? []) ?>,
                    borderColor: accent,
                    backgroundColor: 'rgba(124,106,247,0.08)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: accent,
                    pointBorderColor: 'var(--card)',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { display: false } },
                scales: scalesOpts
            }
        });

        const ctxMetas = document.getElementById('graficoMetas').getContext('2d');
        window._chartMetas = new Chart(ctxMetas, {
            type: 'bar',
            data: {
                labels: <?= json_encode($grafico_metas['labels'] ?? []) ?>,
                datasets: [
                    {
                        label: 'Guardado',
                        data: <?= json_encode($grafico_metas['guardado'] ?? []) ?>,
                        backgroundColor: accent + 'cc',
                        borderRadius: 6,
                        borderSkipped: false
                    },
                    {
                        label: 'Limite',
                        data: <?= json_encode($grafico_metas['limites'] ?? []) ?>,
                        backgroundColor: 'rgba(248,113,113,0.2)',
                        borderRadius: 6,
                        borderSkipped: false
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { position: 'top', labels: { color: muted, font: { family: 'Roboto', size: 12 } } } },
                scales: scalesOpts
            }
        });
    }

    buildCharts();

    window.addEventListener('granaflow:themechange', () => {
        if (window._chartSaldo) window._chartSaldo.destroy();
        if (window._chartMetas) window._chartMetas.destroy();
        setTimeout(buildCharts, 60);
    });
    </script>
    <!-- AJAX Handler para Dashboard -->
    <script src="<?= BASE_URL ?>js/dashboard-ajax.js"></script>
    <!-- Controlar exibição/ocultação de campos condicionais -->
    <script>
        const tipoRecorrenteSelect = document.getElementById('tipo_rec');
        const quantidadeContainer = document.getElementById('quantidade-container');
        const quantidadeInput = quantidadeContainer.querySelector('input');
        
        if (tipoRecorrenteSelect) {
            tipoRecorrenteSelect.addEventListener('change', () => {
                if (tipoRecorrenteSelect.value === 'parcelado') {
                    quantidadeContainer.style.display = 'block';
                    quantidadeInput.required = true;
                } else {
                    quantidadeContainer.style.display = 'none';
                    quantidadeInput.required = false;
                }
            });
        }
    </script>

    <!-- ═══ MODAL: EDITAR GASTO ═══ -->
    <div class="modal fade" id="modalEditarGasto" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content" style="background:var(--card);border:1px solid var(--border);">
                <div class="modal-header" style="border-bottom:1px solid var(--border);">
                    <h5 class="modal-title" style="color:var(--text1);">Editar Gasto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditarGasto" class="form-ajax" method="POST">
                        <input type="hidden" name="_ajax" value="1">
                        <input type="hidden" name="id_gasto" id="editGastoId">
                        <div class="mb-3">
                            <label class="form-label">Categoria</label>
                            <select class="form-select" name="id_categoria" id="editGastoCategoria" required>
                                <option value="">Selecione...</option>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?= $cat['id_categoria'] ?>"><?= htmlspecialchars($cat['nome']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descrição</label>
                            <input type="text" class="form-control" name="descricao" id="editGastoDescricao" required>
                        </div>
                        <div class="row g-2">
                            <div class="col-6 mb-3">
                                <label class="form-label">Valor</label>
                                <input type="number" step="0.01" class="form-control" name="valor" id="editGastoValor" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Data</label>
                                <input type="date" class="form-control" name="data_gasto" id="editGastoData" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-check2 me-1"></i> Salvar Alterações
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══ MODAL: EDITAR RECORRENTE ═══ -->
    <div class="modal fade" id="modalEditarRecorrente" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content" style="background:var(--card);border:1px solid var(--border);">
                <div class="modal-header" style="border-bottom:1px solid var(--border);">
                    <h5 class="modal-title" style="color:var(--text1);">Editar Gasto Recorrente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditarRecorrente" class="form-ajax" method="POST">
                        <input type="hidden" name="_ajax" value="1">
                        <div class="mb-3">
                            <label class="form-label">Descrição</label>
                            <input type="text" class="form-control" name="descricao" id="editRecDescricao" required>
                        </div>
                        <div class="row g-2">
                            <div class="col-6 mb-3">
                                <label class="form-label">Valor</label>
                                <input type="number" step="0.01" class="form-control" name="valor" id="editRecValor" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Dia Vencimento</label>
                                <input type="number" class="form-control" name="dia_vencimento" id="editRecDia" min="1" max="31" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-warning w-100">
                            <i class="bi bi-check2 me-1"></i> Salvar Alterações
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══ MODAL: EDITAR META ═══ -->
    <div class="modal fade" id="modalEditarMeta" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content" style="background:var(--card);border:1px solid var(--border);">
                <div class="modal-header" style="border-bottom:1px solid var(--border);">
                    <h5 class="modal-title" style="color:var(--text1);">Editar Meta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditarMeta" class="form-ajax" method="POST">
                        <input type="hidden" name="_ajax" value="1">
                        <input type="hidden" name="id_meta" id="editMetaId">
                        <div class="mb-3">
                            <label class="form-label">Nome da Meta</label>
                            <input type="text" class="form-control" name="nome_meta" id="editMetaNome" required maxlength="50">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Valor Limite</label>
                            <input type="number" step="0.01" class="form-control" name="valor_limite" id="editMetaLimite" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-check2 me-1"></i> Salvar Alterações
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
        </main>
    </div>
</body>
</html>
