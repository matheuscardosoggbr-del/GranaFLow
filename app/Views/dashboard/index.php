<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — GranaFlow</title>
    <script src="<?= BASE_URL ?>js/theme.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/Style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        *, *::before, *::after { font-family: 'Roboto', sans-serif !important; }
    </style>
</head>

<body data-base-url="<?= BASE_URL ?>"
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand navbar-brand-new" href="#">
                <div class="logo ">
                    <svg width="45" height="auto" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg">
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
                <div class="card card-resumo h-100">
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
                <div class="card card-resumo h-100">
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
                <div class="card card-resumo h-100">
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
                <div class="card card-resumo h-100">
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
                        <form method="POST" action="<?= BASE_URL ?>dashboard/salvarSalario" class="form-ajax">
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
                            <div style="font-size:1.4rem;font-weight:700;color:var(--green);">
                                R$ <?= number_format($total_guardado, 2, ',', '.') ?>
                            </div>
                        </div>
                        <form method="POST" action="<?= BASE_URL ?>dashboard/guardarAvulso" class="form-ajax">
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
                        <form method="POST" action="<?= BASE_URL ?>gastos/adicionar" class="form-ajax">
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
                        <form method="POST" action="<?= BASE_URL ?>dashboard/adicionarRecorrente" class="form-ajax">
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
                        <form method="POST" action="<?= BASE_URL ?>metas/adicionar" class="form-ajax">
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
                                                    <a href="javascript:void(0);"
                                                        class="btn btn-sm btn-outline-danger btn-delete-ajax"
                                                        data-url="<?= BASE_URL ?>gastos/deletar/<?= $gasto['id_gasto'] ?>"
                                                        data-id="<?= $gasto['id_gasto'] ?>"
                                                        data-type="gasto">
                                                        <i class="bi bi-trash3"></i>
                                                    </a>
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
                            <table class="table table-hover tabela-gastos mb-0">
                                <thead>
                                    <tr>
                                        <th>Dia Venc.</th>
                                        <th>Categoria</th>
                                        <th>Descrição</th>
                                        <th>Tipo</th>
                                        <th>Valor</th>
                                        <th>Última Geração</th>
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
                                                <input type="hidden" name="id_meta" value="<?= $meta['id_meta'] ?>">
                                                <input type="number" step="0.01" min="0.01" name="valor"
                                                    class="form-control form-control-sm"
                                                    style="width:90px;" placeholder="R$ valor" required>
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    <i class="bi bi-plus-circle"></i>
                                                </button>
                                            </form>
                                            <a href="<?= BASE_URL ?>metas/deletar/<?= $meta['id_meta'] ?>"
                                                class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Excluir esta meta?')">
                                                <i class="bi bi-trash3"></i>
                                            </a>
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
</body>
</html>
