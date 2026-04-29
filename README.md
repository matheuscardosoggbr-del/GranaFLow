# 📊 GranaFlow - Sistema de Controle de Gastos Pessoais

## 📋 Visão Geral

GranaFlow é um sistema web profissional para controle de gastos pessoais e planejamento financeiro, desenvolvido em PHP com arquitetura MVC. Este projeto foi totalmente reformulado e otimizado para funcionar como uma aplicação TCC completa e profissional.

## ✨ Funcionalidades Implementadas

### 🔐 Autenticação e Segurança
- ✅ Sistema de login e cadastro seguro
- ✅ Validação de emails e senhas
- ✅ Proteção CSRF em todos os formulários
- ✅ Sanitização de entrada de dados
- ✅ Criptografia de senhas com PASSWORD_DEFAULT
- ✅ Sessões seguras

### 💰 Gestão de Gastos (CRUD Completo)
- ✅ **Criar**: Adicionar novos gastos com categoria, descrição, valor e data
- ✅ **Ler**: Visualizar todos os gastos com filtros avançados
- ✅ **Atualizar**: Editar gastos existentes
- ✅ **Deletar**: Remover gastos
- ✅ Filtros por categoria, mês e data
- ✅ Ordenação por data, valor ou categoria
- ✅ Gastos recorrentes (mensais e parcelados)
- ✅ Geração automática de gastos recorrentes

### 📍 Categorias Personalizadas (CRUD)
- ✅ Criar categorias customizadas
- ✅ Editar categorias
- ✅ Deletar categorias (com validação de gastos associados)
- ✅ Categorias padrão do sistema
- ✅ Tipos: Receita e Despesa

### 🎯 Metas e Reservas (CRUD Completo)
- ✅ **Criar**: Novas metas com valor limite
- ✅ **Ler**: Visualizar todas as metas com progresso
- ✅ **Atualizar**: Editar metas
- ✅ **Deletar**: Remover metas
- ✅ Dois tipos de metas:
  - Controle: Limitar gastos em uma categoria
  - Reserva: Guardar dinheiro progressivamente
- ✅ Barra de progresso visual
- ✅ Guardar dinheiro em metas
- ✅ Acompanhamento de progresso

### 💵 Gestão de Salário
- ✅ Definir e atualizar salário mensal
- ✅ Histórico de salários
- ✅ Validação de valores
- ✅ Interface intuitiva

### 📈 Dashboard Inteligente
- ✅ Resumo visual com cards informativos
- ✅ Gráficos interativos (Chart.js)
- ✅ Saldo mensal
- ✅ Gastos mensais vs gastos totais
- ✅ Progresso de metas
- ✅ Últimos 6 meses de histórico
- ✅ Comparação com gastos recorrentes

### 📊 Relatórios e Exportação
- ✅ Página de relatórios detalhados
- ✅ Estatísticas de gastos
- ✅ Gastos por categoria
- ✅ Progresso de metas
- ✅ Exportar em CSV
- ✅ Exportar em JSON
- ✅ Gráficos interativos

### 🎨 Interface Profissional
- ✅ Design responsivo com Bootstrap 5
- ✅ Tema claro/escuro
- ✅ Paleta de cores profissional
- ✅ Ícones modernos (Bootstrap Icons)
- ✅ Navegação intuitiva
- ✅ Menu lateral responsivo
- ✅ Notificações de sucesso/erro

## 🗂️ Estrutura do Projeto

```
project_fixed/
├── app/
│   ├── Controllers/
│   │   ├── AuthController.php        # Autenticação
│   │   ├── GastosController.php      # Gerenciamento de gastos
│   │   ├── CategoriaController.php   # Gerenciamento de categorias
│   │   ├── MetasController.php       # Gerenciamento de metas
│   │   ├── SalarioController.php     # Gerenciamento de salário
│   │   ├── RelatorioController.php   # Relatórios e exportação
│   │   └── DashboardController.php   # Dashboard
│   ├── Models/
│   │   ├── User.php                  # Usuários
│   │   ├── Gasto.php                 # Gastos
│   │   ├── Categoria.php             # Categorias
│   │   ├── Meta.php                  # Metas
│   │   ├── Salario.php               # Salário
│   │   ├── Poupanca.php              # Poupança/Reservas
│   │   └── [Base Models]
│   ├── Core/
│   │   ├── App.php                   # Roteador
│   │   ├── Controller.php            # Classe base com validações
│   │   └── Model.php                 # Classe base com DB
│   ├── Views/
│   │   ├── auth/                     # Login e cadastro
│   │   ├── dashboard/                # Dashboard
│   │   ├── gastos/                   # Gastos (index + form)
│   │   ├── categorias/               # Categorias
│   │   ├── metas/                    # Metas (index + form)
│   │   ├── salario/                  # Salário
│   │   └── relatorios/               # Relatórios
│   ├── config.php                    # Configuração
│   └── init.php                      # Inicialização
├── public/
│   ├── index.php                     # Entrada
│   ├── css/
│   │   └── Style.css                 # Estilos
│   └── js/
│       ├── theme.js                  # Toggle tema
│       └── script.js                 # Scripts
└── sql/
    └── banco_completo.sql            # Banco de dados
```

## 🗄️ Banco de Dados

### Tabelas Principais
- `usuarios` - Usuários do sistema
- `categorias` - Categorias de gastos
- `gastos` - Registro de gastos
- `gastos_recorrentes` - Gastos recorrentes
- `metas` - Metas financeiras
- `salarios` - Salário dos usuários
- `dinheiro_guardado` - Poupança
- `moedas` - Tipos de moeda
- `tipos_categoria` - Tipos de categoria

## 🔧 Instalação e Configuração

### Pré-requisitos
- PHP 7.4+
- MySQL 5.7+
- Apache com mod_rewrite

### Passos

1. **Clone ou extraia o projeto**
   ```bash
   cd xampp/htdocs/GITTRABALHOS/project/project_fixed
   ```

2. **Configure o banco de dados**
   - Abra o phpMyAdmin
   - Crie um novo banco de dados ou importe `sql/banco_completo.sql`
   - O banco é automaticamente criado se usar a importação

3. **Configure a conexão**
   - Edite `app/Core/Model.php` se necessário alterar credenciais
   - Padrão: `host=localhost, user=root, password='', database=granaflow, port=3308`

4. **Acesse a aplicação**
   ```
   http://localhost/GITTRABALHOS/project/project_fixed/
   ```

5. **Crie sua conta**
   - Clique em "Cadastre-se grátis"
   - Preencha os dados (nome 3-30 caracteres, email válido, senha 6+ caracteres)
   - Confirme a senha

## 📝 Melhorias Implementadas

### Segurança
- [x] Proteção CSRF em todos os formulários
- [x] Validação e sanitização de entrada
- [x] Prepared statements em todas as queries
- [x] Criptografia de senhas
- [x] Controle de acesso por usuário
- [x] Validação de email
- [x] Validação de valores monetários
- [x] Validação de datas

### Funcionalidades CRUD Completas
- [x] Gastos: Create, Read, Update, Delete
- [x] Categorias: Create, Read, Update, Delete
- [x] Metas: Create, Read, Update, Delete
- [x] Salário: Create, Read, Update

### Interface e UX
- [x] Menu de navegação em todas as páginas
- [x] Mensagens de sucesso/erro
- [x] Confirmação de exclusão
- [x] Filtros avançados
- [x] Ordenação de dados
- [x] Paginação (pronta para adicionar)
- [x] Responsividade total
- [x] Tema claro/escuro

### Relatórios
- [x] Dashboard com gráficos
- [x] Página de relatórios detalhada
- [x] Exportação CSV
- [x] Exportação JSON
- [x] Estatísticas por categoria

### Validações
- [x] Email válido
- [x] Senha com força mínima
- [x] Valores monetários positivos
- [x] Datas válidas
- [x] Comprimento de texto
- [x] Acesso apenas para usuários autenticados
- [x] Verificação de propriedade de recurso

## 🚀 Como Usar

### Primeiro Acesso
1. Cadastre-se com seus dados
2. Defina seu salário em "Salário"
3. Crie suas categorias personalizadas (opcional)
4. Comece a adicionar gastos

### Adicionar Gasto
1. Vá para "Gastos" → "Novo Gasto"
2. Selecione categoria, valor, data e descrição
3. Clique em "Salvar"

### Criar Meta
1. Vá para "Metas" → "Nova Meta"
2. Escolha tipo (Controle ou Reserva)
3. Defina o valor limite
4. Clique em "Salvar"

### Ver Relatórios
1. Acesse a página "Relatórios"
2. Veja gráficos e estatísticas
3. Exporte em CSV ou JSON

## 🔒 Segurança

O projeto implementa várias camadas de segurança:

- **Autenticação**: Login com email e senha criptografada
- **CSRF**: Token único para cada formulário
- **SQL Injection**: Prepared statements em todas as queries
- **XSS**: Sanitização de saída com htmlspecialchars
- **Acesso**: Verificação de sessão em todas as rotas protegidas
- **Autorização**: Verificação de propriedade de recurso por usuário

## 📱 Responsividade

A aplicação é totalmente responsiva e funciona em:
- 📱 Smartphones (320px+)
- 📱 Tablets (768px+)
- 💻 Desktop (1200px+)

## 🎓 Aspectos de TCC

Este projeto demonstra:

1. **Arquitetura MVC**: Separação clara de Model, View, Controller
2. **POO**: Uso de classes, herança e encapsulamento
3. **Segurança**: Múltiplas camadas de proteção
4. **Validação**: Entrada e saída validadas
5. **Database**: Design normalizado e queries otimizadas
6. **UX**: Interface intuitiva e responsiva
7. **Documentação**: Código bem comentado e documentação clara

## 🐛 Troubleshooting

### Erro de conexão ao banco
- Verifique se MySQL está rodando na porta 3308
- Confirme credenciais em `app/Core/Model.php`
- Verifique se o banco `granaflow` existe

### Erro 404 em rotas
- Verifique se mod_rewrite está ativado no Apache
- Confirme a URL base em `app/config.php`
- Verifique permissões de pastas

### Problema com upload de arquivo CSS/JS
- Verifique permissões da pasta `public/`
- Limpe cache do navegador (Ctrl+F5)

## 📞 Suporte

Para dúvidas ou problemas:
1. Verifique o arquivo `.htaccess` no public/
2. Confirme configurações do servidor
3. Teste a conexão ao banco

## 📄 Licença

Projeto de TCC - Uso livre para fins educacionais.

---

**Desenvolvido com ❤️ para fins educacionais**
