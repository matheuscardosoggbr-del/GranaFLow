/**
 * GranaFlow - Dashboard AJAX Handler
 * Gerencia todas as requisições AJAX do dashboard sem redirecionar
 */

class DashboardAjax {
    constructor() {
        this.baseUrl = document.querySelector('[data-base-url]')?.dataset.baseUrl || '/granaflow/';
        this.initEventListeners();
    }

    /**
     * Inicializa listeners para todos os formulários AJAX
     */
    initEventListeners() {
        // Delegação de eventos para formulários com classe 'form-ajax'
        document.addEventListener('submit', (e) => {
            const form = e.target;
            if (form.classList.contains('form-ajax')) {
                e.preventDefault();
                this.submitForm(form);
            }
        });

        // Botões de deletar
        document.addEventListener('click', (e) => {
            if (e.target.closest('.btn-delete-ajax')) {
                e.preventDefault();
                const btn = e.target.closest('.btn-delete-ajax');
                this.deleteItem(btn);
            }
        });

        // Switches para ativar/desativar
        document.addEventListener('change', (e) => {
            if (e.target.classList.contains('switch-ajax')) {
                this.toggleItem(e.target);
            }
        });
    }

    /**
     * Envia um formulário via AJAX
     */
    async submitForm(form) {
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        try {
            // Mostrar loading
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processando...';

            const formData = new FormData(form);
            const action = form.getAttribute('action');

            // Adicionar flag de AJAX
            formData.append('_ajax', '1');

            const response = await fetch(action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if (data.success) {
                // Mostrar mensagem de sucesso
                this.showToast(data.message || 'Operação realizada com sucesso!', 'success');

                // Limpar formulário
                form.reset();

                // Recarregar dados na página
                if (data.action === 'salario') {
                    this.updateSalario(data.data);
                } else if (data.action === 'gasto') {
                    this.updateGastos(data.data);
                } else if (data.action === 'guardado') {
                    this.updateGuardado(data.data);
                } else if (data.action === 'meta') {
                    this.updateMetas(data.data);
                } else if (data.action === 'recorrente') {
                    this.updateRecorrentes(data.data);
                }

                // Fechar modal se existir
                const modal = form.closest('.modal');
                if (modal) {
                    const bsModal = bootstrap.Modal.getInstance(modal);
                    if (bsModal) bsModal.hide();
                }
            } else {
                this.showToast(data.message || 'Erro ao processar a requisição', 'error');
            }
        } catch (error) {
            console.error('Erro:', error);
            this.showToast('Erro ao processar a requisição: ' + error.message, 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    }

    /**
     * Deleta um item via AJAX
     */
    async deleteItem(btn) {
        if (!confirm('Tem certeza que deseja deletar este item?')) {
            return;
        }

        const url = btn.getAttribute('data-url');
        const itemId = btn.getAttribute('data-id');
        const itemType = btn.getAttribute('data-type');

        try {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            const response = await fetch(url + '?_ajax=1', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ _ajax: 1 })
            });

            const data = await response.json();

            if (data.success) {
                this.showToast(data.message || 'Item deletado com sucesso!', 'success');

                // Remover linha da tabela com animação
                const row = btn.closest('tr');
                if (row) {
                    row.style.opacity = '0';
                    row.style.transition = 'opacity 0.3s ease-out';
                    setTimeout(() => row.remove(), 300);
                }

                // Atualizar dados
                if (itemType === 'gasto') {
                    this.updateGastos();
                } else if (itemType === 'recorrente') {
                    this.updateRecorrentes();
                }
            } else {
                this.showToast(data.message || 'Erro ao deletar item', 'error');
            }
        } catch (error) {
            console.error('Erro:', error);
            this.showToast('Erro ao deletar item: ' + error.message, 'error');
            btn.disabled = false;
        }
    }

    /**
     * Toggle de ativação/desativação
     */
    async toggleItem(checkbox) {
        const url = checkbox.getAttribute('data-url');
        const enabled = checkbox.checked;

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ enabled })
            });

            const data = await response.json();

            if (!data.success) {
                checkbox.checked = !enabled;
                this.showToast(data.message || 'Erro ao atualizar', 'error');
            }
        } catch (error) {
            console.error('Erro:', error);
            checkbox.checked = !enabled;
            this.showToast('Erro ao atualizar item', 'error');
        }
    }

    /**
     * Atualiza o valor do salário na página
     */
    updateSalario(data) {
        const salarioCards = document.querySelectorAll('[data-type="salario"]');
        salarioCards.forEach(card => {
            const valor = card.querySelector('.valor');
            if (valor) {
                valor.textContent = 'R$ ' + this.formatarMoeda(data.salario);
            }
        });

        // Atualizar gráficos se necessário
        this.updateDashboardCards(data);
    }

    /**
     * Atualiza a tabela de gastos
     */
    updateGastos(data) {
        // Se houver dados retornados, atualizar cards primeiro
        if (data) {
            this.updateDashboardCards(data);
        }
        // Recarregar apenas a tabela (sem recarregar página)
        this.reloadTable('gastos');
    }

    /**
     * Atualiza dinheiro guardado
     */
    updateGuardado(data) {
        const guardadoDisplay = document.querySelector('[data-type="guardado"] .valor');
        if (guardadoDisplay) {
            guardadoDisplay.textContent = 'R$ ' + this.formatarMoeda(data.total_guardado);
        }
        this.updateDashboardCards(data);
        // Recarregar tabela de histórico
        this.reloadTable('historico-guardado');
    }

    /**
     * Atualiza metas
     */
    updateMetas(data) {
        if (data) {
            this.updateDashboardCards(data);
        }
        this.reloadTable('metas');
    }

    /**
     * Atualiza recorrentes
     */
    updateRecorrentes(data) {
        if (data) {
            this.updateDashboardCards(data);
        }
        this.reloadTable('recorrentes');
    }

    /**
     * Recarrega uma tabela específica via AJAX
     */
    reloadTable(tableId) {
        // Simples: recarregar página (pode ser melhorado depois)
        // Esta é uma implementação simples que recarrega a página
        // Em uma versão futura, isso pode fazer requisição AJAX para recarregar apenas a tabela
        location.reload();
    }

    /**
     * Atualiza cards do dashboard
     */
    updateDashboardCards(data) {
        if (data.saldo !== undefined) {
            const saldoCard = document.querySelector('[data-card="saldo"] .valor');
            if (saldoCard) {
                saldoCard.textContent = 'R$ ' + this.formatarMoeda(data.saldo);
                saldoCard.className = data.saldo >= 0 ? 'valor saldo-positivo' : 'valor saldo-negativo';
            }
        }

        if (data.total_mes !== undefined) {
            const mesCard = document.querySelector('[data-card="total-mes"] .valor');
            if (mesCard) {
                mesCard.textContent = 'R$ ' + this.formatarMoeda(data.total_mes);
            }
        }
    }

    /**
     * Mostra toast notification
     */
    showToast(message, type = 'info') {
        const toastContainer = document.getElementById('toast-container') || this.createToastContainer();
        
        const toastHtml = `
            <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;

        const toastElement = document.createElement('div');
        toastElement.innerHTML = toastHtml;
        toastContainer.appendChild(toastElement.firstElementChild);

        const toast = new bootstrap.Toast(toastContainer.lastElementChild);
        toast.show();

        // Remover elemento após animação
        setTimeout(() => {
            toastContainer.lastElementChild.remove();
        }, 5000);
    }

    /**
     * Cria container para toasts
     */
    createToastContainer() {
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        document.body.appendChild(container);
        return container;
    }

    /**
     * Formata número para moeda brasileira
     */
    formatarMoeda(valor) {
        return Number(valor).toLocaleString('pt-BR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    /**
     * Abre modal para editar item
     */
    openEditModal(itemId, itemType) {
        const modalId = `modal-edit-${itemType}`;
        const modal = document.getElementById(modalId);
        if (modal) {
            const bsModal = new bootstrap.Modal(modal);
            // Carregar dados do item
            this.loadItemData(itemId, itemType, modal);
            bsModal.show();
        }
    }

    /**
     * Carrega dados do item para edição
     */
    async loadItemData(itemId, itemType, modal) {
        try {
            const response = await fetch(`${this.baseUrl}${itemType}/get/${itemId}?_ajax=1`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();
            if (data.success) {
                this.populateModal(modal, data.data);
            }
        } catch (error) {
            console.error('Erro ao carregar dados:', error);
        }
    }

    /**
     * Popula modal com dados
     */
    populateModal(modal, data) {
        Object.keys(data).forEach(key => {
            const input = modal.querySelector(`[name="${key}"]`);
            if (input) {
                input.value = data[key];
            }
        });
    }
}

// Inicializar quando DOM estiver pronto
document.addEventListener('DOMContentLoaded', () => {
    window.dashboardAjax = new DashboardAjax();
});
