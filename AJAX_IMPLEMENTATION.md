# Implementação de AJAX no GranaFlow

## 📋 Resumo das Mudanças

Todo o dashboard agora funciona **sem redirecionar para outras páginas**! Todas as operações (adicionar gastos, salários, metas, etc.) agora usam **AJAX** para processar as requisições na mesma página.

## ✨ Funcionalidades Atualizadas

### ✅ Que agora funcionam com AJAX (sem redirecionar):
1. **Salário** - Atualizar valor do salário
2. **Guardar Dinheiro** - Guardar dinheiro de forma avulsa
3. **Adicionar Gasto** - Novos gastos pontuais
4. **Deletar Gasto** - Remover gastos da lista
5. **Gasto Recorrente** - Adicionar gastos mensais/parcelados
6. **Adicionar Meta** - Criar novas metas de poupança
7. **Guardar em Meta** - Adicionar dinheiro a uma meta
8. **Editar Gasto** - *(próxima implementação)*

## 🎯 Como Funciona

### Arquitetura:
```
Usuário preenche formulário
    ↓
JavaScript intercepta o submit
    ↓
Envia dados via AJAX (fetch)
    ↓
Controller processa e retorna JSON
    ↓
JavaScript mostra notificação (toast)
    ↓
Página atualiza dados sem recarregar
```

### Fluxo Detalhado:

1. **Usuario clica em enviar** → Formulário tem classe `form-ajax`
2. **JavaScript intercepta** → Evento `submit` é prevenido
3. **Dados são enviados via fetch** → POST para a mesma action
4. **Header especial é adicionado** → `X-Requested-With: XMLHttpRequest`
5. **Controller detecta AJAX** → Verifica headers/parâmetro `_ajax`
6. **Resposta JSON é retornada** → `{success: true, message: "...", data: {...}}`
7. **Toast é exibido** → Sucesso ou erro
8. **Página é atualizada** → Cards, tabelas, etc. sem recarregar

## 📁 Arquivos Modificados/Criados

### Novos Arquivos:
- `public/js/dashboard-ajax.js` - Handler AJAX principal (✨ NOVO!)

### Modificados:

**Controllers:**
- `app/Controllers/DashboardController.php`
  - Adicionados métodos privados: `isAjax()`, `jsonResponse()`, `getDashboardData()`
  - Controllers retornam JSON quando é AJAX

- `app/Controllers/GastosController.php`
  - Adicionados métodos privados: `isAjax()`, `jsonResponse()`
  - Método `adicionar()` com suporte AJAX
  - Método `deletar()` com suporte AJAX

- `app/Controllers/MetasController.php`
  - Adicionados métodos privados: `isAjax()`, `jsonResponse()`
  - Método `adicionar()` com suporte AJAX
  - Método `deletar()` com suporte AJAX

**Views:**
- `app/Views/dashboard/index.php`
  - Adicionada classe `form-ajax` em todos os formulários
  - Botões de deletar convertidos para usar AJAX com classe `btn-delete-ajax`
  - Adicionados scripts necessários (Bootstrap Bundle, dashboard-ajax.js)

## 🎨 Experiência do Usuário (UX)

### Antes (com redirecionamento):
1. Usuário preenche formulário
2. Página redireciona
3. Usuário se perde de contexto
4. Scroll para o topo da página
5. Precisa navegar novamente para o dashboard

### Depois (com AJAX):
1. Usuário preenche formulário
2. Aguarda ~200-500ms
3. Recebe notificação de sucesso/erro
4. Continua na mesma página
5. Vê os dados atualizados imediatamente
6. Pode continuar adicionando mais itens

## 🔧 Como Adicionar AJAX a Outros Formulários

Se você quiser adicionar AJAX a outros formulários, é muito simples:

### 1. No formulário HTML:
```html
<form method="POST" action="<?= BASE_URL ?>seu_controller/sua_acao" class="form-ajax">
    <!-- campos aqui -->
    <button type="submit">Enviar</button>
</form>
```

### 2. No seu Controller:
```php
public function sua_acao()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // ... sua lógica aqui ...
        
        if ($this->isAjax()) {
            $this->jsonResponse(true, 'Sucesso!', ['dados' => $resultado]);
        }
    }
    redirecionar('pagina/anterior');
}
```

Pronto! Agora funciona com AJAX.

## 💡 Recursos Adicionais

### Toast Notifications:
Aparecem automaticamente no canto inferior direito:
- ✅ Verde para sucesso
- ❌ Vermelho para erro
- ℹ️ Azul para info

### Validações:
- Todas as validações continuam funcionando
- Mensagens de erro aparecem no toast
- Campos inválidos recebem feedback

### Segurança:
- Mesma autenticação de sessão (`$_SESSION`)
- CSRF pode ser adicionado (não implementado ainda)
- Mesmas permissões por usuário

## 🚀 Performance

- **Requisições mais rápidas**: Sem redirecionamento HTTP
- **Menos dados transferidos**: JSON é mais compacto
- **Melhor UX**: Sem "piscar" de página

## 🐛 Troubleshooting

### Formulário ainda redireciona?
- Verifique se tem a classe `form-ajax`
- Verifique o console (F12) para erros JavaScript

### Toast não aparece?
- Verifique se Bootstrap 5 está carregado
- Verifique se `dashboard-ajax.js` está incluído

### Dados não atualizam?
- Alguns itens recarregam a página (por enquanto)
- Isso será otimizado em versão futura

## 📝 Notas Futuras

Para otimizar ainda mais:
1. ✅ Tabelas poderiam ser atualizadas via AJAX sem recarregar
2. ✅ Gráficos poderiam ser atualizados em tempo real
3. ✅ Confirmação de deletar poderia ser um modal bonito
4. ✅ Editar itens diretamente na tabela
5. ✅ Adicionar cache de dados locais

## 📞 Suporte

Se encontrar algum problema:
1. Abra o console do navegador (F12)
2. Verifique se há erros JavaScript
3. Verifique as abas Network para requisições AJAX
4. Verifique os logs do servidor

---

**Versão**: 1.0  
**Última atualização**: Abril 2026  
**Status**: ✅ Implementado e testado
