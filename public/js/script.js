/**
 * Lógica de Navegação entre Abas - GranaFlow
 * CORRIGIDO: const garante que 'titulos' não pode ser redeclarado acidentalmente.
 * NOVO: History API — URL muda ao trocar de aba, botão Voltar funciona.
 */

const titulos = {
    dashboard:  ["Painel inicial",  "Visão geral dos seus gastos"],
    gastos:     ["Meus Gastos",     "Gerencie e adicione seus gastos"],
    relatorios: ["Relatórios",      "Análise detalhada de gastos"],
    metas:      ["Minhas Metas",    "Defina e acompanhe suas metas"],
    categorias: ["Categorias",      "Gerencie suas categorias"]
};

/**
 * Troca a aba visível, atualiza o menu e (opcionalmente) a URL.
 * @param {string}          pagina   - chave da aba (ex: 'gastos')
 * @param {HTMLElement|null} botao   - botão .item-menu clicado (pode ser null)
 * @param {boolean}         pushUrl  - se true, empurra estado na History API
 */
function mudarPagina(pagina, botao, pushUrl = false) {
    // 1. Esconder todas as páginas
    document.querySelectorAll('.pagina').forEach(p => p.classList.remove('ativa'));

    // 2. Mostrar a página selecionada
    const alvo = document.getElementById('pagina-' + pagina);
    if (alvo) alvo.classList.add('ativa');

    // 3. Atualizar estado visual do menu lateral
    document.querySelectorAll('.item-menu').forEach(b => b.classList.remove('ativo'));
    if (botao) botao.classList.add('ativo');

    // 4. Atualizar títulos na Navbar (se existir)
    if (titulos[pagina]) {
        const tituloElem    = document.querySelector('.navbar h5');
        const subtituloElem = document.querySelector('.navbar small');
        if (tituloElem)    tituloElem.innerText    = titulos[pagina][0];
        if (subtituloElem) subtituloElem.innerText = titulos[pagina][1];
    }

    // 5. NOVO: atualizar URL sem recarregar a página
    if (pushUrl) {
        history.pushState({ pagina }, '', '?aba=' + pagina);
    }
}

/**
 * Encontra o botão .item-menu que corresponde a uma aba pelo data-pagina.
 */
function botaoDaPagina(pagina) {
    return document.querySelector(`.item-menu[data-pagina="${pagina}"]`) || null;
}

document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const status    = urlParams.get('status');
    const aba       = urlParams.get('aba');

    // Prioridade: ?status=sucesso|deletado → gastos  |  ?aba=xxx  |  padrão dashboard
    let paginaInicial = 'dashboard';
    if (status === 'sucesso' || status === 'deletado') {
        paginaInicial = 'gastos';
    } else if (aba && titulos[aba]) {
        paginaInicial = aba;
    }

    mudarPagina(paginaInicial, botaoDaPagina(paginaInicial));

    // Registra estado inicial para o botão Voltar funcionar
    history.replaceState({ pagina: paginaInicial }, '', window.location.href);
});

// NOVO: botão Voltar/Avançar do navegador navega entre abas
window.addEventListener('popstate', (e) => {
    if (e.state?.pagina) {
        mudarPagina(e.state.pagina, botaoDaPagina(e.state.pagina));
    }
});
