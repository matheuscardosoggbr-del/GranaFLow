(function () {
    function getScope() {
        return document.querySelector('.crm-page') || document.querySelector('.dashboard-shell') || document.body;
    }

    function isMatch(text, query) {
        return !query || text.toLowerCase().includes(query);
    }

    function filterTargets(scope, query) {
        const selectors = [
            '.card-resumo',
            '.formulario-card',
            '.grafico-container',
            '.crm-quick-card',
            '.crm-panel',
            '.list-group-item',
            'tbody tr',
            '.meta-item'
        ];

        const seen = new Set();
        selectors.forEach((selector) => {
            scope.querySelectorAll(selector).forEach((el) => {
                if (seen.has(el)) return;
                seen.add(el);
                const text = (el.textContent || '').trim();
                const visible = isMatch(text, query);
                el.style.display = visible ? '' : 'none';
            });
        });
    }

    function bindSearch(input) {
        const scope = getScope();
        const run = () => filterTargets(scope, input.value.trim().toLowerCase());

        input.addEventListener('input', run);
        input.addEventListener('search', run);

        const form = input.closest('form');
        if (form) {
            form.addEventListener('submit', (e) => {
                if (input.value.trim()) {
                    e.preventDefault();
                    run();
                }
            });
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-global-search]').forEach(bindSearch);
        const sidebarSearch = document.querySelector('.crm-search input');
        if (sidebarSearch && !sidebarSearch.dataset.bound) {
            sidebarSearch.dataset.bound = '1';
            bindSearch(sidebarSearch);
        }
    });
})();
