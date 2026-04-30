/**
 * GranaFlow — Dashboard AJAX Handler com Atualização em Tempo Real
 * v2.0 — Corrigido e com suporte a edição inline via modais
 */

class DashboardAjax {
    constructor() {
        this.baseUrl = document.querySelector('[data-base-url]')?.dataset.baseUrl || '/granaflow/';
        this.initEventListeners();
    }

    initEventListeners() {
        // Formulários AJAX
        document.addEventListener('submit', (e) => {
            const form = e.target;
            if (form.classList.contains('form-ajax')) {
                e.preventDefault();
                this.submitForm(form);
            }
        });

        // Botões de deletar
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.btn-delete-ajax');
            if (btn) {
                e.preventDefault();
                this.deleteItem(btn);
            }
        });

        // Botões de editar — abre modal correto conforme data-type
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.btn-edit-ajax');
            if (btn) {
                e.preventDefault();
                this.openEditModal(btn);
            }
        });
    }

    /**
     * Envia formulário via AJAX e atualiza dados em tempo real
     */
    async submitForm(form) {
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn ? submitBtn.innerHTML : '';

        try {
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processando...';
            }

            const formData = new FormData(form);
            const action = form.getAttribute('action');
            formData.append('_ajax', '1');

            const response = await fetch(action, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (!response.ok) throw new Error('Erro HTTP ' + response.status);

            const data = await response.json();

            if (data.success) {
                this.showToast(data.message || 'Operação realizada com sucesso!', 'success');
                form.reset();

                // Fechar modal se o form está dentro de um
                const modal = form.closest('.modal');
                if (modal) {
                    bootstrap.Modal.getInstance(modal)?.hide();
                }

                // Atualizar interface com os dados retornados
                if (data.data) {
                    this.updateCards(data.data);
                    this.updateTables(data.data);
                }
            } else {
                this.showToast(data.message || 'Erro ao processar a requisição', 'error');
            }
        } catch (error) {
            console.error('Erro:', error);
            this.showToast('Erro: ' + error.message, 'error');
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        }
    }

    /**
     * Atualiza os cards de resumo
     */
    updateCards(data) {
        const mappings = {
            'saldo':      { value: data.saldo,          colored: true },
            'total-mes':  { value: data.total_mes,       colored: false },
            'total-geral':{ value: data.total_geral,     colored: false },
            'salario':    { value: data.salario,         colored: false },
        };

        Object.entries(mappings).forEach(([key, cfg]) => {
            const card = document.querySelector(`[data-card="${key}"] .valor`);
            if (card && cfg.value !== undefined) {
                card.textContent = 'R$ ' + this.formatarMoeda(cfg.value);
                if (cfg.colored) {
                    card.className = cfg.value >= 0 ? 'valor saldo-positivo' : 'valor saldo-negativo';
                }
            }
        });

        // Total guardado no painel de guardar dinheiro
        const totalGuardado = document.querySelector('[data-type="guardado"] .valor');
        if (totalGuardado && data.total_guardado !== undefined) {
            totalGuardado.textContent = 'R$ ' + this.formatarMoeda(data.total_guardado);
        }
    }

    /**
     * Atualiza todas as tabelas/listas
     */
    updateTables(data) {
        if (data.gastos !== undefined)              this.updateGastosTable(data.gastos);
        if (data.recorrentes !== undefined)         this.updateRecorrentesTable(data.recorrentes);
        if (data.metas !== undefined)               this.updateMetasTable(data.metas);
        if (data.historico_guardado !== undefined)  this.updateHistoricoGuardado(data.historico_guardado);
        if (data.metas !== undefined)               this.updateMetasSelect(data.metas);
    }

    /**
     * Atualiza tabela de gastos recentes
     */
    updateGastosTable(gastos) {
        // Seleciona apenas a primeira tabela tabela-gastos (não a de recorrentes)
        const tabela = document.querySelector('table.tabela-gastos:not(.tabela-recorrentes)');
        const tbody = tabela ? tabela.querySelector('tbody') : null;
        if (!tbody) return;

        tbody.innerHTML = '';

        if (!gastos || gastos.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5"><div class="empty-state"><i class="bi bi-inbox"></i><p>Nenhum gasto registrado ainda</p></div></td></tr>`;
            return;
        }

        gastos.slice(0, 10).forEach(g => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><span style="color:var(--muted);font-size:12px;"><i class="bi bi-calendar3 me-1"></i>${this.formatarData(g.data_gasto)}</span></td>
                <td><span class="badge badge-categoria">${this.esc(g.categoria)}</span></td>
                <td style="color:var(--text2);">${this.esc(g.descricao || '—')}</td>
                <td><span class="valor-gasto">${this.esc(g.simbolo || 'R$')} ${this.formatarMoeda(g.valor)}</span></td>
                <td>
                    <div class="btn-group btn-group-sm" role="group">
                        <button class="btn btn-outline-primary btn-edit-ajax"
                            data-url="${this.baseUrl}gastos/adicionar"
                            data-id="${g.id_gasto}" data-type="gasto"
                            data-descricao="${this.esc(g.descricao || '')}"
                            data-valor="${g.valor}"
                            data-data="${g.data_gasto}"
                            data-categoria="${g.id_categoria || ''}"
                            title="Editar"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-outline-danger btn-delete-ajax"
                            data-url="${this.baseUrl}gastos/deletar/${g.id_gasto}"
                            data-id="${g.id_gasto}" data-type="gasto"
                            title="Deletar"><i class="bi bi-trash3"></i></button>
                    </div>
                </td>`;
            tbody.appendChild(tr);
        });
    }

    /**
     * Atualiza tabela de gastos recorrentes
     */
    updateRecorrentesTable(recorrentes) {
        const tbody = document.querySelector('table.tabela-recorrentes tbody');
        if (!tbody) return;

        tbody.innerHTML = '';

        if (!recorrentes || recorrentes.length === 0) {
            tbody.innerHTML = `<tr><td colspan="7"><div class="empty-state"><i class="bi bi-arrow-repeat"></i><p>Nenhum gasto recorrente ativo</p></div></td></tr>`;
            return;
        }

        recorrentes.forEach(r => {
            const tipoBadge = r.tipo === 'parcelado'
                ? `<span class="badge" style="background:rgba(248,113,113,0.15);color:var(--red);font-size:11px;">Parcelado · ${r.quantidade_meses}x</span>`
                : `<span class="badge" style="background:rgba(251,191,36,0.15);color:var(--yellow);font-size:11px;">Mensal</span>`;

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><span style="color:var(--muted);font-size:12px;"><i class="bi bi-calendar3 me-1"></i>Dia ${r.dia_vencimento}</span></td>
                <td><span class="badge badge-categoria">${this.esc(r.categoria)}</span></td>
                <td style="color:var(--text2);">${this.esc(r.descricao)}</td>
                <td>${tipoBadge}</td>
                <td><span class="valor-gasto">R$ ${this.formatarMoeda(r.valor)}</span></td>
                <td><span style="color:var(--muted);font-size:12px;">${r.ultima_execucao ? this.formatarData(r.ultima_execucao) : '—'}</span></td>
                <td>
                    <div class="btn-group btn-group-sm" role="group">
                        <button class="btn btn-outline-primary btn-edit-ajax"
                            data-url="${this.baseUrl}dashboard/editarRecorrente/${r.id}"
                            data-id="${r.id}" data-type="recorrente"
                            data-descricao="${this.esc(r.descricao)}"
                            data-valor="${r.valor}" data-dia="${r.dia_vencimento}"
                            title="Editar"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-outline-danger btn-delete-ajax"
                            data-url="${this.baseUrl}dashboard/deletarRecorrente/${r.id}"
                            data-id="${r.id}" data-type="recorrente"
                            title="Deletar"><i class="bi bi-trash3"></i></button>
                    </div>
                </td>`;
            tbody.appendChild(tr);
        });
    }

    /**
     * Atualiza lista de metas
     */
    updateMetasTable(metas) {
        const container = document.getElementById('metas-list');
        if (!container) return;

        container.innerHTML = '';

        if (!metas || metas.length === 0) {
            container.innerHTML = `<div class="empty-state"><i class="bi bi-flag"></i><p>Nenhuma meta cadastrada ainda</p></div>`;
            return;
        }

        metas.forEach(m => {
            const pct = m.valor_limite > 0 ? Math.min((m.valor_guardado / m.valor_limite) * 100, 100) : 0;
            const done = pct >= 100;

            const div = document.createElement('div');
            div.className = 'meta-item';
            div.innerHTML = `
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <div>
                        <h6 class="mb-1">
                            ${done ? '<i class="bi bi-check-circle-fill me-1" style="color:var(--green);font-size:13px;"></i>' : ''}
                            ${this.esc(m.nome_meta)}
                        </h6>
                        <small class="text-muted">R$ ${this.formatarMoeda(m.valor_guardado)} <span style="color:var(--muted2);">de</span> R$ ${this.formatarMoeda(m.valor_limite)}</small>
                    </div>
                    <div class="d-flex gap-2 align-items-center">
                        <form method="POST" action="${this.baseUrl}dashboard/guardarDinheiro" class="d-flex gap-1 align-items-center form-ajax">
                            <input type="hidden" name="_ajax" value="1">
                            <input type="hidden" name="id_meta" value="${m.id_meta}">
                            <input type="number" step="0.01" min="0.01" name="valor"
                                class="form-control form-control-sm" placeholder="0,00" style="width:80px;" required>
                            <button type="submit" class="btn btn-sm btn-success" title="Guardar">
                                <i class="bi bi-plus"></i>
                            </button>
                        </form>
                        <div class="btn-group btn-group-sm" role="group">
                            <button class="btn btn-outline-primary btn-edit-ajax"
                                data-url="${this.baseUrl}metas/salvar"
                                data-id="${m.id_meta}" data-type="meta"
                                data-nome="${this.esc(m.nome_meta)}"
                                data-limite="${m.valor_limite}"
                                title="Editar"><i class="bi bi-pencil"></i></button>
                            <button class="btn btn-outline-danger btn-delete-ajax"
                                data-url="${this.baseUrl}metas/deletar/${m.id_meta}"
                                data-id="${m.id_meta}" data-type="meta"
                                title="Deletar"><i class="bi bi-trash3"></i></button>
                        </div>
                    </div>
                </div>
                <div class="progress-meta">
                    <div class="progress-bar" role="progressbar" style="width:${pct}%"></div>
                </div>
                <small style="color:${done ? 'var(--green)' : 'var(--muted)'};font-size:11px;font-weight:500;">
                    ${done
                        ? '<i class="bi bi-star-fill me-1"></i> Meta concluída!'
                        : `${pct.toFixed(1)}% concluído · faltam R$ ${this.formatarMoeda(m.valor_limite - m.valor_guardado)}`}
                </small>`;
            container.appendChild(div);
        });
    }

    /**
     * Atualiza histórico de dinheiro guardado
     */
    updateHistoricoGuardado(historico) {
        const tbody = document.querySelector('table.tabela-historico-guardado tbody');
        if (!tbody) return;

        tbody.innerHTML = '';
        if (!historico || historico.length === 0) return;

        historico.forEach(h => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><span style="color:var(--muted);font-size:12px;"><i class="bi bi-calendar3 me-1"></i>${this.formatarData(h.data_registro)}</span></td>
                <td style="color:var(--text2);">${this.esc(h.descricao)}</td>
                <td><span style="color:var(--green);font-weight:600;">R$ ${this.formatarMoeda(h.valor)}</span></td>`;
            tbody.appendChild(tr);
        });
    }

    /**
     * Atualiza select de metas no formulário "Guardar Dinheiro"
     */
    updateMetasSelect(metas) {
        const selects = document.querySelectorAll('select[name="id_meta"]');
        selects.forEach(select => {
            const currentValue = select.value;
            select.innerHTML = '<option value="">Guardar sem destino</option>';
            if (metas && metas.length > 0) {
                metas.forEach(m => {
                    const opt = document.createElement('option');
                    opt.value = m.id_meta;
                    opt.textContent = `${m.nome_meta} (R$ ${this.formatarMoeda(m.valor_guardado)} / R$ ${this.formatarMoeda(m.valor_limite)})`;
                    select.appendChild(opt);
                });
            }
            if (currentValue && select.querySelector(`option[value="${currentValue}"]`)) {
                select.value = currentValue;
            }
        });
    }

    /**
     * Abre o modal de edição adequado conforme o tipo do item
     */
    openEditModal(btn) {
        const type = btn.getAttribute('data-type');

        if (type === 'gasto') {
            const modal = document.getElementById('modalEditarGasto');
            if (!modal) return;
            const id   = btn.getAttribute('data-id');
            document.getElementById('editGastoId').value        = id;
            document.getElementById('editGastoDescricao').value = btn.getAttribute('data-descricao') || '';
            document.getElementById('editGastoValor').value     = btn.getAttribute('data-valor') || '';
            document.getElementById('editGastoData').value      = btn.getAttribute('data-data') || '';
            const catSelect = document.getElementById('editGastoCategoria');
            if (catSelect) catSelect.value = btn.getAttribute('data-categoria') || '';
            // Aponta o form para a rota de salvar gasto (com id_gasto oculto)
            document.getElementById('formEditarGasto').setAttribute('action', this.baseUrl + 'gastos/adicionar');
            new bootstrap.Modal(modal).show();

        } else if (type === 'recorrente') {
            const modal = document.getElementById('modalEditarRecorrente');
            if (!modal) return;
            const url = btn.getAttribute('data-url');
            document.getElementById('editRecDescricao').value = btn.getAttribute('data-descricao') || '';
            document.getElementById('editRecValor').value     = btn.getAttribute('data-valor') || '';
            document.getElementById('editRecDia').value       = btn.getAttribute('data-dia') || '';
            document.getElementById('formEditarRecorrente').setAttribute('action', url);
            new bootstrap.Modal(modal).show();

        } else if (type === 'meta') {
            const modal = document.getElementById('modalEditarMeta');
            if (!modal) return;
            const id = btn.getAttribute('data-id');
            document.getElementById('editMetaId').value     = id;
            document.getElementById('editMetaNome').value   = btn.getAttribute('data-nome') || '';
            document.getElementById('editMetaLimite').value = btn.getAttribute('data-limite') || '';
            document.getElementById('formEditarMeta').setAttribute('action', this.baseUrl + 'metas/salvar');
            new bootstrap.Modal(modal).show();
        }
    }

    /**
     * Deleta um item via AJAX
     */
    async deleteItem(btn) {
        if (!confirm('Tem certeza que deseja deletar este item?')) return;

        const url  = btn.getAttribute('data-url');
        const type = btn.getAttribute('data-type');

        try {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            const fd = new FormData();
            fd.append('_ajax', '1');

            const response = await fetch(url + (url.includes('?') ? '&' : '?') + '_ajax=1', {
                method: 'POST',
                body: fd,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            const data = await response.json();

            if (data.success) {
                this.showToast(data.message || 'Item deletado com sucesso!', 'success');

                // Remover linha/item com animação
                const row = btn.closest('tr') || btn.closest('.meta-item');
                if (row) {
                    row.style.transition = 'opacity 0.3s';
                    row.style.opacity = '0';
                    setTimeout(() => row.remove(), 300);
                }

                // Atualizar dados
                if (data.data) {
                    setTimeout(() => {
                        this.updateCards(data.data);
                        this.updateTables(data.data);
                    }, 350);
                }
            } else {
                this.showToast(data.message || 'Erro ao deletar item', 'error');
                btn.disabled = false;
            }
        } catch (error) {
            console.error('Erro:', error);
            this.showToast('Erro: ' + error.message, 'error');
            btn.disabled = false;
        }
    }

    showToast(message, type = 'info') {
        const container = document.getElementById('toast-container') || this.createToastContainer();
        const bg   = type === 'success' ? 'bg-success' : type === 'error' ? 'bg-danger' : 'bg-info';
        const icon = type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle';

        const el = document.createElement('div');
        el.innerHTML = `
            <div class="toast align-items-center text-white ${bg} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body"><i class="bi bi-${icon} me-2"></i>${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>`;
        container.appendChild(el.firstElementChild);
        new bootstrap.Toast(container.lastElementChild).show();
        setTimeout(() => container.lastElementChild?.remove(), 5000);
    }

    createToastContainer() {
        const c = document.createElement('div');
        c.id = 'toast-container';
        c.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        c.style.zIndex = '9999';
        document.body.appendChild(c);
        return c;
    }

    formatarMoeda(valor) {
        return Number(valor).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    formatarData(data) {
        if (!data) return '—';
        const d = new Date(data + 'T00:00:00');
        return d.toLocaleDateString('pt-BR');
    }

    esc(str) {
        const div = document.createElement('div');
        div.textContent = str || '';
        return div.innerHTML;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    window.dashboardAjax = new DashboardAjax();
});
