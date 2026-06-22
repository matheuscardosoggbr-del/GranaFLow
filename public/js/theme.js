// =======================================================
// GranaFlow - theme.js
// Tema global com seletor de temas e persistência no browser
// =======================================================

(function () {
    const STORAGE_KEY = 'granaflow_theme';

    const THEMES = {
        midnight: { label: 'Midnight', icon: 'moon-stars' },
        light: { label: 'Light', icon: 'sun' },
        ocean: { label: 'Ocean', icon: 'droplet' },
        lavender: { label: 'Lavender', icon: 'flower1' }
    };

    function applyTheme(theme) {
        const normalized = THEMES[theme] ? theme : 'midnight';
        document.documentElement.setAttribute('data-theme', normalized);
        localStorage.setItem(STORAGE_KEY, normalized);
    }

    function emitThemeChange(theme) {
        window.dispatchEvent(new CustomEvent('granaflow:themechange', { detail: { theme } }));
    }

    function setTheme(theme) {
        applyTheme(theme);
        emitThemeChange(theme);
    }

    function getSavedTheme() {
        return localStorage.getItem(STORAGE_KEY) || 'midnight';
    }

    function injectThemePicker() {
        const host = document.querySelector('.crm-topbar-actions') || document.querySelector('.navbar-nav');
        if (!host || document.getElementById('theme-picker')) return;

        const current = document.documentElement.getAttribute('data-theme') || getSavedTheme();
        const picker = document.createElement('div');
        picker.id = 'theme-picker';
        picker.className = 'theme-picker';
        picker.innerHTML = `
            <button type="button" class="theme-picker-toggle" aria-haspopup="listbox" aria-expanded="false">
                <i class="bi bi-palette"></i>
                <span>Tema</span>
                <i class="bi bi-chevron-down"></i>
            </button>
            <div class="theme-picker-menu" role="listbox"></div>
        `;

        const menu = picker.querySelector('.theme-picker-menu');
        Object.entries(THEMES).forEach(([key, cfg]) => {
            const item = document.createElement('button');
            item.type = 'button';
            item.className = 'theme-picker-item' + (key === current ? ' active' : '');
            item.dataset.theme = key;
            item.innerHTML = `<i class="bi bi-${cfg.icon}"></i><span>${cfg.label}</span>`;
            item.addEventListener('click', () => {
                setTheme(key);
                picker.querySelectorAll('.theme-picker-item').forEach(btn => btn.classList.remove('active'));
                item.classList.add('active');
                picker.classList.remove('open');
                picker.querySelector('.theme-picker-toggle').setAttribute('aria-expanded', 'false');
            });
            menu.appendChild(item);
        });

        picker.querySelector('.theme-picker-toggle').addEventListener('click', () => {
            const open = picker.classList.toggle('open');
            picker.querySelector('.theme-picker-toggle').setAttribute('aria-expanded', String(open));
        });

        document.addEventListener('click', (e) => {
            if (!picker.contains(e.target)) {
                picker.classList.remove('open');
                picker.querySelector('.theme-picker-toggle').setAttribute('aria-expanded', 'false');
            }
        });

        if (host.classList.contains('navbar-nav')) {
            const li = document.createElement('li');
            li.className = 'nav-item d-flex align-items-center me-2';
            li.appendChild(picker);
            host.insertBefore(li, host.firstChild);
        } else {
            host.prepend(picker);
        }
    }

    function initFlyouts() {
        document.querySelectorAll('[data-crm-flyout]').forEach((btn) => {
            const key = btn.getAttribute('data-crm-flyout');
            const menu = document.querySelector(`[data-flyout="${key}"]`);
            if (!menu) return;

            btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const isOpen = menu.classList.toggle('open');
                btn.setAttribute('aria-expanded', String(isOpen));

                document.querySelectorAll('.crm-flyout.open').forEach((other) => {
                    if (other !== menu) other.classList.remove('open');
                });
                document.querySelectorAll('[data-crm-flyout]').forEach((otherBtn) => {
                    if (otherBtn !== btn) otherBtn.setAttribute('aria-expanded', 'false');
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

    const saved = getSavedTheme();
    applyTheme(saved);

    document.addEventListener('DOMContentLoaded', function () {
        injectThemePicker();
        initFlyouts();
    });
})();
