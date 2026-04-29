<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — GranaFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/Style.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>*, *::before, *::after { font-family: 'Roboto', sans-serif !important; }</style>
</head>
<body class="auth-page">
    <div style="position:relative;z-index:1;width:100%;max-width:420px;padding:20px;margin:auto;">

        <div class="card auth-card">
            <!-- Header -->
            <div class="auth-header">
                <div class="auth-brand">
                    <div class="logo">
                            <svg width="60" height="auto" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg">
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
                    <span class="auth-brand-name">GranaFlow</span>
                </div>
                <h2 class="mt-3 mb-0" style="font-size:1.05rem; color:var(--muted); font-weight:400;">
                    Entre na sua conta
                </h2>
            </div>

            <!-- Body -->
            <div class="auth-body">
                <?php if (isset($erro)): ?>
                    <div class="alert alert-danger mb-3">
                        <i class="bi bi-exclamation-circle me-2"></i><?= $erro ?>
                    </div>
                <?php endif; ?>

                <form action="<?= BASE_URL ?>auth/login" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <div class="mb-3">
                        <label class="form-label">E-mail</label>
                        <input type="email" name="email" class="form-control" placeholder="seu@email.com" required autofocus>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Senha</label>
                        <input type="password" name="senha" class="form-control" placeholder="••••••••" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        Entrar <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                </form>

                <div class="text-center mt-4">
                    <span style="color:var(--muted); font-size:13px;">
                        Não tem conta?
                        <a href="<?= BASE_URL ?>auth/cadastro" style="color:var(--accent); font-weight:600; text-decoration:none;">
                            Cadastre-se grátis
                        </a>
                    </span>
                </div>
            </div>
        </div>

    </div>
</body>
</html>
