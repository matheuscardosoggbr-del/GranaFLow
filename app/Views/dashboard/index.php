<?php
$pageTitle = 'Painel';
$pageKicker = 'Gestao Financeira';
$pageHeading = 'Visao Geral';
$activePage = 'dashboard';

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

<section class="crm-section-title">
    <h3>Resumo Financeiro</h3>
    <span class="crm-badge"><i class="bi bi-shield-check"></i>Dados atualizados</span>
</section>

<div class="crm-grid-quick mb-4">
    <article class="crm-quick-card" data-card="saldo">
        <div class="card-resumo-icon" style="background:rgba(108,99,255,0.12);color:var(--accent);">
            <i class="bi bi-wallet2"></i>
        </div>
        <div class="label">Saldo</div>
        <div class="value valor <?= $saldo >= 0 ? 'saldo-positivo' : 'saldo-negativo' ?>">
            R$ <?= number_format((float)$saldo, 2, ',', '.') ?>
        </div>
    </article>

    <article class="crm-quick-card" data-card="total-mes">
        <div class="card-resumo-icon" style="background:rgba(248,113,113,0.10);color:var(--red);">
            <i class="bi bi-calendar3"></i>
        </div>
        <div class="label">Gasto Mes</div>
        <div class="value valor">R$ <?= number_format((float)$total_mes, 2, ',', '.') ?></div>
    </article>

    <article class="crm-quick-card" data-card="total-geral">
        <div class="card-resumo-icon" style="background:rgba(251,191,36,0.10);color:var(--yellow);">
            <i class="bi bi-graph-up-arrow"></i>
        </div>
        <div class="label">Gasto Total</div>
        <div class="value valor">R$ <?= number_format((float)$total_geral, 2, ',', '.') ?></div>
    </article>

    <article class="crm-quick-card" data-card="salario">
        <div class="card-resumo-icon" style="background:rgba(52,211,153,0.10);color:var(--green);">
            <i class="bi bi-cash-stack"></i>
        </div>
        <div class="label">Salario</div>
        <div class="value valor saldo-positivo">R$ <?= number_format((float)$salario, 2, ',', '.') ?></div>
    </article>

    <article class="crm-quick-card" data-card="receitas">
        <div class="card-resumo-icon" style="background:rgba(14,165,233,0.10);color:#0ea5e9;">
            <i class="bi bi-arrow-down-circle"></i>
        </div>
        <div class="label">Entradas Mes</div>
        <div class="value valor saldo-positivo">R$ <?= number_format((float)($total_receitas_mes ?? 0), 2, ',', '.') ?></div>
    </article>
</div>

<div class="crm-grid-panels mb-4">
    <section class="crm-panel">
        <div class="panel-head">
            <span><i class="bi bi-bar-chart-line me-2"></i>Saldo Mensal</span>
        </div>
        <div class="panel-body">
            <div class="grafico-container" style="height:320px;">
                <canvas id="graficoSaldo"></canvas>
            </div>
        </div>
    </section>

    <section class="crm-panel">
        <div class="panel-head">
            <span><i class="bi bi-bullseye me-2"></i>Progresso das Metas</span>
        </div>
        <div class="panel-body">
            <div class="grafico-container" style="height:320px;">
                <canvas id="graficoMetas"></canvas>
            </div>
        </div>
    </section>
</div>

<div class="crm-section-title">
    <h3>Acoes Rapidas</h3>
    <span class="crm-badge"><i class="bi bi-lightning-charge"></i>Fluxo principal</span>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="card formulario-card h-100">
            <div class="card-header">
                <div class="card-header-icon" style="background:rgba(52,211,153,0.12);color:var(--green);">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <h6>Atualizar Salario</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= BASE_URL ?>dashboard/salvarSalario" class="form-ajax" id="form-salario">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <div class="mb-3">
                        <label class="form-label">Valor do salario</label>
                        <input type="number" step="0.01" class="form-control" name="salario" value="<?= number_format((float)$salario, 2, '.', '') ?>" required placeholder="0,00">
                    </div>
                    <button type="submit" class="btn btn-success btn-acao w-100">
                        <i class="bi bi-check2 me-1"></i>Salvar salário
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card formulario-card h-100">
            <div class="card-header">
                <div class="card-header-icon" style="background:rgba(52,211,153,0.12);color:var(--green);">
                    <i class="bi bi-piggy-bank"></i>
                </div>
                <h6>Guardar Dinheiro <span class="page-subtitle">- subtraído do saldo</span></h6>
            </div>
            <div class="card-body">
                <div class="mb-3 p-3 rounded" style="background:rgba(52,211,153,0.08);border:1px solid rgba(52,211,153,0.18);" data-type="guardado">
                    <div class="page-subtitle mb-1">Total guardado</div>
                    <div class="value valor" style="font-size:1.5rem;color:var(--green);">
                        R$ <?= number_format((float)$total_guardado, 2, ',', '.') ?>
                    </div>
                </div>
                <form method="POST" action="<?= BASE_URL ?>dashboard/guardarAvulso" class="form-ajax" id="form-guardado">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <div class="mb-3">
                        <label class="form-label">Selecione uma meta</label>
                        <select class="form-select" name="id_meta">
                            <option value="">Guardar sem destino</option>
                            <?php foreach ($metas as $meta): ?>
                                <option value="<?= $meta['id_meta'] ?>">
                                    <?= htmlspecialchars($meta['nome_meta']) ?>
                                    (R$ <?= number_format((float)$meta['valor_guardado'], 2, ',', '.') ?> / R$ <?= number_format((float)$meta['valor_limite'], 2, ',', '.') ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descrição</label>
                        <input type="text" class="form-control" name="descricao" placeholder="Ex: Reserva de emergência">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Valor a guardar</label>
                        <input type="number" step="0.01" min="0.01" class="form-control" name="valor" placeholder="0,00" required>
                    </div>
                    <button type="submit" class="btn btn-success btn-acao w-100">
                        <i class="bi bi-piggy-bank me-1"></i>Guardar dinheiro
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="card formulario-card h-100">
            <div class="card-header">
                <div class="card-header-icon" style="background:rgba(248,113,113,0.10);color:var(--red);">
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
                        <i class="bi bi-plus-circle me-1"></i>Adicionar gasto
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card formulario-card h-100">
            <div class="card-header">
                <div class="card-header-icon" style="background:rgba(251,191,36,0.10);color:var(--yellow);">
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
                            <input type="number" class="form-control" name="dia_vencimento" min="1" max="31" placeholder="1-31" required>
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
                        <label class="form-label">Quantidade de meses</label>
                        <input type="number" class="form-control" name="quantidade" min="1" placeholder="Ex: 12">
                    </div>
                    <button type="submit" class="btn btn-warning btn-acao w-100">
                        <i class="bi bi-arrow-repeat me-1"></i>Cadastrar recorrente
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-6">
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
                        <label class="form-label">Nome da meta</label>
                        <input type="text" class="form-control" name="nome_meta" placeholder="Ex: Viagem, reserva de emergência" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Valor limite</label>
                        <input type="number" step="0.01" class="form-control" name="valor_limite" placeholder="0,00" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-acao w-100">
                        <i class="bi bi-plus-circle me-1"></i>Adicionar meta
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
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
                                <th style="width: 120px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($gastos)): ?>
                                <tr>
                                    <td colspan="5">
                                        <div class="empty-state">
                                            <i class="bi bi-inbox"></i>
                                            <p>Nenhum gasto registrado ainda</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach (array_slice($gastos, 0, 10) as $gasto): ?>
                                    <tr>
                                        <td>
                                            <span style="color:var(--muted);font-size:12px;">
                                                <i class="bi bi-calendar3 me-1"></i><?= date('d/m/Y', strtotime($gasto['data_gasto'])) ?>
                                            </span>
                                        </td>
                                        <td><span class="badge-categoria"><?= htmlspecialchars($gasto['categoria']) ?></span></td>
                                        <td><?= htmlspecialchars($gasto['descricao'] ?? '—') ?></td>
                                        <td><span class="valor-gasto"><?= $gasto['simbolo'] ?> <?= number_format((float)$gasto['valor'], 2, ',', '.') ?></span></td>
                                        <td class="text-end">
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

<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="card formulario-card">
            <div class="card-header">
                <div class="card-header-icon" style="background:rgba(251,191,36,0.10);color:var(--yellow);">
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
                                <th style="width: 120px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recorrentes)): ?>
                                <tr>
                                    <td colspan="7">
                                        <div class="empty-state">
                                            <i class="bi bi-arrow-repeat"></i>
                                            <p>Nenhum gasto recorrente ativo</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recorrentes as $rec): ?>
                                    <tr>
                                        <td>
                                            <span style="color:var(--muted);font-size:12px;">
                                                <i class="bi bi-calendar3 me-1"></i>Dia <?= (int)$rec['dia_vencimento'] ?>
                                            </span>
                                        </td>
                                        <td><span class="badge-categoria"><?= htmlspecialchars($rec['categoria']) ?></span></td>
                                        <td><?= htmlspecialchars($rec['descricao']) ?></td>
                                        <td>
                                            <?php if ($rec['tipo'] === 'parcelado'): ?>
                                                <span class="badge" style="background:rgba(248,113,113,0.12);color:var(--red);">Parcelado · <?= (int)$rec['quantidade_meses'] ?>x</span>
                                            <?php else: ?>
                                                <span class="badge" style="background:rgba(251,191,36,0.12);color:var(--yellow);">Mensal</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><span class="valor-gasto">R$ <?= number_format((float)$rec['valor'], 2, ',', '.') ?></span></td>
                                        <td>
                                            <span style="color:var(--muted);font-size:12px;">
                                                <?= !empty($rec['ultima_execucao']) ? date('d/m/Y', strtotime($rec['ultima_execucao'])) : '—' ?>
                                            </span>
                                        </td>
                                        <td class="text-end">
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

<?php if (!empty($historico_guardado)): ?>
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card formulario-card">
                <div class="card-header">
                    <div class="card-header-icon" style="background:rgba(52,211,153,0.12);color:var(--green);">
                        <i class="bi bi-piggy-bank"></i>
                    </div>
                    <h6>Últimos Registros - Dinheiro Guardado</h6>
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
                                        <td>
                                            <span style="color:var(--muted);font-size:12px;">
                                                <i class="bi bi-calendar3 me-1"></i><?= date('d/m/Y', strtotime($h['data_registro'])) ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($h['descricao']) ?></td>
                                        <td><span style="color:var(--green);font-weight:700;">R$ <?= number_format((float)$h['valor'], 2, ',', '.') ?></span></td>
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

<div class="row g-3">
    <div class="col-12">
        <div class="card formulario-card">
            <div class="card-header">
                <div class="card-header-icon" style="background:rgba(52,211,153,0.10);color:var(--green);">
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
                            $percentual = ((float)$meta['valor_limite'] > 0)
                                ? min(((float)$meta['valor_guardado'] / (float)$meta['valor_limite']) * 100, 100)
                                : 0;
                            $isComplete = $percentual >= 100;
                            ?>
                            <div class="meta-item">
                                <div class="d-flex justify-content-between align-items-start mb-2 gap-3">
                                    <div>
                                        <h6 class="mb-1">
                                            <?php if ($isComplete): ?>
                                                <i class="bi bi-check-circle-fill me-1" style="color:var(--green);font-size:13px;"></i>
                                            <?php endif; ?>
                                            <?= htmlspecialchars($meta['nome_meta']) ?>
                                        </h6>
                                        <small class="text-muted">
                                            R$ <?= number_format((float)$meta['valor_guardado'], 2, ',', '.') ?>
                                            <span style="color:var(--muted2);">de</span>
                                            R$ <?= number_format((float)$meta['valor_limite'], 2, ',', '.') ?>
                                        </small>
                                    </div>
                                    <div class="d-flex gap-2 align-items-center flex-wrap">
                                        <form method="POST" action="<?= BASE_URL ?>dashboard/guardarDinheiro" class="d-flex gap-2 align-items-center" style="display:inline;">
                                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                            <input type="hidden" name="id_meta" value="<?= $meta['id_meta'] ?>">
                                            <input type="number" step="0.01" min="0.01" name="valor" class="form-control form-control-sm" style="width:110px;" placeholder="R$ valor" required>
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
                                    <div class="progress-bar" role="progressbar" style="width:<?= (float)$percentual ?>%"></div>
                                </div>
                                <small style="color:<?= $isComplete ? 'var(--green)' : 'var(--muted)' ?>;font-size:11px;font-weight:500;">
                                    <?php if ($isComplete): ?>
                                        <i class="bi bi-star-fill me-1"></i>Meta concluída!
                                    <?php else: ?>
                                        <?= round($percentual, 1) ?>% concluído · faltam R$ <?= number_format(((float)$meta['valor_limite'] - (float)$meta['valor_guardado']), 2, ',', '.') ?>
                                    <?php endif; ?>
                                </small>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function getCSSVar(name) {
    return getComputedStyle(document.documentElement).getPropertyValue(name).trim();
}

function buildCharts() {
    const elSaldo = document.getElementById('graficoSaldo');
    const elMetas = document.getElementById('graficoMetas');
    if (!elSaldo || !elMetas || typeof Chart === 'undefined') return;

    const accent = getCSSVar('--accent') || '#6c63ff';
    const red = getCSSVar('--red') || '#f87171';
    const muted = getCSSVar('--muted') || '#7a82a0';
    const border = getCSSVar('--border2') || 'rgba(255,255,255,0.11)';

    const scalesOpts = {
        x: { ticks: { color: muted, font: { family: 'Roboto', size: 11 } }, grid: { color: border } },
        y: { ticks: { color: muted, font: { family: 'Roboto', size: 11 }, callback: v => 'R$ ' + Number(v).toLocaleString('pt-BR') }, grid: { color: border } }
    };

    if (window._chartSaldo) window._chartSaldo.destroy();
    if (window._chartMetas) window._chartMetas.destroy();

    window._chartSaldo = new Chart(elSaldo.getContext('2d'), {
        type: 'line',
        data: {
            labels: <?= json_encode($grafico_saldo['labels'] ?? []) ?>,
            datasets: [{
                label: 'Saldo',
                data: <?= json_encode($grafico_saldo['valores'] ?? []) ?>,
                borderColor: accent,
                backgroundColor: 'rgba(108,99,255,0.08)',
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
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: scalesOpts
        }
    });

    window._chartMetas = new Chart(elMetas.getContext('2d'), {
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
                    backgroundColor: 'rgba(248,113,113,0.20)',
                    borderRadius: 6,
                    borderSkipped: false
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'top', labels: { color: muted, font: { family: 'Roboto', size: 12 } } } },
            scales: scalesOpts
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const tipoRecorrenteSelect = document.getElementById('tipo_rec');
    const quantidadeContainer = document.getElementById('quantidade-container');
    const quantidadeInput = quantidadeContainer ? quantidadeContainer.querySelector('input') : null;

    if (tipoRecorrenteSelect && quantidadeContainer && quantidadeInput) {
        const syncField = () => {
            const isParcelado = tipoRecorrenteSelect.value === 'parcelado';
            quantidadeContainer.style.display = isParcelado ? 'block' : 'none';
            quantidadeInput.required = isParcelado;
        };

        tipoRecorrenteSelect.addEventListener('change', syncField);
        syncField();
    }

    buildCharts();
});

window.addEventListener('granaflow:themechange', () => {
    setTimeout(buildCharts, 40);
});
</script>
<script src="<?= BASE_URL ?>js/dashboard-ajax.js"></script>


<div class="modal fade" id="modalEditarGasto" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="background:var(--card);border:1px solid var(--border);">
            <div class="modal-header" style="border-bottom:1px solid var(--border);">
                <h5 class="modal-title">Editar Gasto</h5>
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
                        <i class="bi bi-check2 me-1"></i>Salvar alterações
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modalEditarRecorrente" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="background:var(--card);border:1px solid var(--border);">
            <div class="modal-header" style="border-bottom:1px solid var(--border);">
                <h5 class="modal-title">Editar Gasto Recorrente</h5>
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
                        <i class="bi bi-check2 me-1"></i>Salvar alterações
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modalEditarMeta" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="background:var(--card);border:1px solid var(--border);">
            <div class="modal-header" style="border-bottom:1px solid var(--border);">
                <h5 class="modal-title">Editar Meta</h5>
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
                        <i class="bi bi-check2 me-1"></i>Salvar alterações
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/partials/shell_bottom.php'; ?>



