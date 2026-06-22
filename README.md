# GranaFlow

Sistema web de gestão financeira pessoal e empresarial desenvolvido em PHP, com arquitetura MVC, MySQL, Bootstrap 5 e JavaScript.

## Visão geral

O GranaFlow organiza gastos, receitas, metas, salário, categorias e histórico de poupança em uma interface responsiva, com suporte a painel principal, relatórios e exportação de dados.

## Funcionalidades

- Autenticação com cadastro, login e logout
- Proteção CSRF em formulários
- Painel principal com resumo financeiro e gráficos
- Cadastro, edição e exclusão de gastos
- Cadastro, edição e exclusão de receitas
- Filtros por categoria, mês, ordenação e busca
- Gastos recorrentes com geração automática mensal
- Cadastro, edição e exclusão de categorias
- Metas financeiras do tipo controle e reserva
- Guarda de dinheiro em metas e histórico de poupança
- Registro e atualização de salário
- Histórico de salário
- Relatórios com indicadores, receitas e gráfico por categoria
- Exportação em CSV e JSON
- Tema visual com alternância de estilos
- Interface responsiva para desktop e celular

## Estrutura principal

```text
app/
  Controllers/   Regras de negócio
  Core/          Base do MVC
  Models/        Acesso ao banco
  Views/         Telas do sistema
public/
  css/           Estilos
  js/            Scripts
sql/
  banco_completo.sql
```

## Banco de dados

O script principal de instalação está em `sql/banco_completo.sql`.

Tabelas principais:

- `usuarios`
- `categorias`
- `gastos`
- `receitas`
- `gastos_recorrentes`
- `metas`
- `salarios`
- `salarios_historico`
- `dinheiro_guardado`
- `moedas`
- `tipos_categoria`

## Instalação

1. Copie o projeto para o XAMPP, por exemplo em `C:\xampp\htdocs\GranaFLow`
2. Inicie Apache e MySQL
3. Importe `sql/banco_completo.sql` no phpMyAdmin
4. Copie `.env.example` para `.env` e ajuste os dados do banco, se necessário
5. Abra `http://localhost/GranaFLow/`

## Melhorias implementadas recentemente

- Remoção de conflito de merge em `GastosController`
- Correção de validação de propriedade em categorias
- Inclusão de tela de edição de categoria
- Histórico de salário
- Melhor tratamento de erro em `Controller` e `Model`
- Indicadores adicionais no relatório
- Sugestão de login após cadastro

## Próximos passos recomendados

- Adicionar módulo de receitas
- Separar relatório por período e categoria
- Criar exportação em PDF
- Melhorar autenticação com recuperação de senha
- Adicionar página de configurações do usuário

## Observação para TCC

O projeto já cobre uma base sólida para demonstração acadêmica: CRUD completo, segurança básica, painel principal, relatórios, exportação e uma interface visual consistente.
