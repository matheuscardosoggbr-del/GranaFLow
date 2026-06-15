// =======================================================
// GranaFlow — theme.js
// CORRIGIDO: ícones Bootstrap já carregados antes deste
// script no <head>, então bi-* sempre renderiza corretamente.
// =======================================================

(function () {
    const STORAGE_KEY = 'granaflow_theme';

    // Aplica o tema ANTES do render para evitar flash
    const saved = localStorage.getItem(STORAGE_KEY) || 'dark';
    document.documentElement.setAttribute('data-theme', saved);

    document.addEventListener('DOMContentLoaded', function () {
        // Cria o botão de toggle
        const btn = document.createElement('button');
        btn.id = 'theme-toggle';
        btn.setAttribute('aria-label', 'Alternar tema');
        _updateButton(btn, saved);

        // Insere como primeiro item da navbar-nav
        const navRight = document.querySelector('.navbar-nav');
        if (navRight) {
            const li = document.createElement('li');
            li.className = 'nav-item d-flex align-items-center me-2';
            li.appendChild(btn);
            navRight.insertBefore(li, navRight.firstChild);
        }

        btn.addEventListener('click', function () {
            const current = document.documentElement.getAttribute('data-theme');
            const next    = current === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', next);
            localStorage.setItem(STORAGE_KEY, next);
            _updateButton(btn, next);

            // NOVO: dispara evento para que outros scripts (ex: gráficos)
            // possam reagir à mudança de tema se necessário.
            window.dispatchEvent(new CustomEvent('granaflow:themechange', { detail: { theme: next } }));
        });
    });

    function _updateButton(btn, theme) {
        if (theme === 'dark') {
            btn.innerHTML = '<i class="bi bi-cloud-moon"></i> <span>Claro</span>';
            btn.title     = 'Mudar para tema claro';
        } else {
            btn.innerHTML = '<i class="bi bi-cloud-sun"></i> <span>Escuro</span>';
            btn.title     = 'Mudar para tema escuro';
        }
    }
})();
