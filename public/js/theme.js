(function () {
    const STORAGE_KEY = 'granaflow_theme';

    const TEMAS = {
        midnight: { label: 'Meia-noite', icon: 'moon-stars' },
        light: { label: 'Claro', icon: 'sun' },
        ocean: { label: 'Oceano', icon: 'droplet' },
        lavender: { label: 'Lavanda', icon: 'flower1' }
    };

    function aplicarTema(tema) {
        const normalizado = TEMAS[tema] ? tema : 'midnight';
        document.documentElement.setAttribute('data-theme', normalizado);
        localStorage.setItem(STORAGE_KEY, normalizado);
    }

    function emitirMudancaTema(tema) {
        window.dispatchEvent(new CustomEvent('granaflow:themechange', { detail: { theme: tema } }));
    }

    function definirTema(tema) {
        aplicarTema(tema);
        emitirMudancaTema(tema);
    }

    function obterTemaSalvo() {
        return localStorage.getItem(STORAGE_KEY) || 'midnight';
    }

    function inserirSeletorTema() {
        const host = document.querySelector('.crm-topbar-actions') || document.querySelector('.navbar-nav');
        if (!host || document.getElementById('theme-picker')) return;

        const atual = document.documentElement.getAttribute('data-theme') || obterTemaSalvo();
        const seletor = document.createElement('div');
        seletor.id = 'theme-picker';
        seletor.className = 'theme-picker';
        seletor.innerHTML = `
            <button type="button" class="theme-picker-toggle" aria-haspopup="listbox" aria-expanded="false">
                <i class="bi bi-palette"></i>
                <span>Tema</span>
                <i class="bi bi-chevron-down"></i>
            </button>
            <div class="theme-picker-menu" role="listbox"></div>
        `;

        const menu = seletor.querySelector('.theme-picker-menu');
        Object.entries(TEMAS).forEach(([chave, cfg]) => {
            const item = document.createElement('button');
            item.type = 'button';
            item.className = 'theme-picker-item' + (chave === atual ? ' active' : '');
            item.dataset.theme = chave;
            item.innerHTML = `<i class="bi bi-${cfg.icon}"></i><span>${cfg.label}</span>`;
            item.addEventListener('click', () => {
                definirTema(chave);
                seletor.querySelectorAll('.theme-picker-item').forEach(btn => btn.classList.remove('active'));
                item.classList.add('active');
                seletor.classList.remove('open');
                seletor.querySelector('.theme-picker-toggle').setAttribute('aria-expanded', 'false');
            });
            menu.appendChild(item);
        });

        seletor.querySelector('.theme-picker-toggle').addEventListener('click', () => {
            const aberto = seletor.classList.toggle('open');
            seletor.querySelector('.theme-picker-toggle').setAttribute('aria-expanded', String(aberto));
        });

        document.addEventListener('click', (e) => {
            if (!seletor.contains(e.target)) {
                seletor.classList.remove('open');
                seletor.querySelector('.theme-picker-toggle').setAttribute('aria-expanded', 'false');
            }
        });

        if (host.classList.contains('navbar-nav')) {
            const li = document.createElement('li');
            li.className = 'nav-item d-flex align-items-center me-2';
            li.appendChild(seletor);
            host.insertBefore(li, host.firstChild);
        } else {
            host.prepend(seletor);
        }
    }

    function iniciarJanelasFlutuantes() {
        document.querySelectorAll('[data-crm-flyout]').forEach((btn) => {
            const chave = btn.getAttribute('data-crm-flyout');
            const menu = document.querySelector(`[data-flyout="${chave}"]`);
            if (!menu) return;

            btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const aberto = menu.classList.toggle('open');
                btn.setAttribute('aria-expanded', String(aberto));

                document.querySelectorAll('.crm-flyout.open').forEach((outro) => {
                    if (outro !== menu) outro.classList.remove('open');
                });
                document.querySelectorAll('[data-crm-flyout]').forEach((outroBtn) => {
                    if (outroBtn !== btn) outroBtn.setAttribute('aria-expanded', 'false');
                });
            });
        });

        document.addEventListener('click', (e) => {
            if (!e.target.closest('.crm-flyout') && !e.target.closest('[data-crm-flyout]')) {
                document.querySelectorAll('.crm-flyout.open').forEach((menu) => menu.classList.remove('open'));
                document.querySelectorAll('[data-crm-flyout]').forEach((btn) => btn.setAttribute('aria-expanded', 'false'));
            }
        });
    }

    const temaSalvo = obterTemaSalvo();
    aplicarTema(temaSalvo);

    document.addEventListener('DOMContentLoaded', function () {
        inserirSeletorTema();
        iniciarJanelasFlutuantes();
    });
})();
