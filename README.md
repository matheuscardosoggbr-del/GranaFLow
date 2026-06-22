# GranaFlow

Sistema web de gestao financeira pessoal com foco em TCC, desenvolvido em PHP com arquitetura MVC, MySQL, Bootstrap 5 e JavaScript.

## Visao geral

O GranaFlow organiza gastos, metas, salario, categorias e historico de poupanca em uma interface responsiva, com suporte a dashboard, relatórios e exportacao de dados.

## Funcionalidades atuais

- Autenticacao com cadastro, login e logout
- Protecao CSRF em formularios
- Dashboard com resumo financeiro e graficos
- Cadastro, edicao e exclusao de gastos
- Cadastro, edicao e exclusao de receitas
- Filtros por categoria, mes, ordenacao e busca
- Gastos recorrentes com geracao automatica mensal
- Cadastro, edicao e exclusao de categorias
- Metas financeiras do tipo controle e reserva
- Guarda de dinheiro em metas e historico de poupanca
- Registro e atualizacao de salario
- Historico de salario
- Relatorios com indicadores, receitas e grafico por categoria
- Exportacao em CSV e JSON
- Tema visual com alternancia de estilos
- Interface responsiva para desktop e celular

## Estrutura principal

```text
app/
  Controllers/   Regras de negocio
  Core/          Base do framework MVC
  Models/        Acesso ao banco
  Views/         Telas do sistema
public/
  css/           Estilos
  js/            Scripts
sql/
  banco_completo.sql
```

## Banco de dados

O script principal de instalacao esta em `sql/banco_completo.sql`.

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

## Instalacao

1. Copie o projeto para o XAMPP, por exemplo em `C:\xampp\htdocs\GranaFLow`
2. Inicie Apache e MySQL
3. Importe `sql/banco_completo.sql` no phpMyAdmin
4. Ajuste a conexao do banco em `app/Core/Model.php` se necessario
5. Abra `http://localhost/GranaFLow/public/`

## Melhorias implementadas recentemente

- Remocao de conflito de merge em `GastosController`
- Correcao de validacao de propriedade em categorias
- Inclusao de tela de edicao de categoria
- Historico de salario
- Melhor tratamento de erro em `Controller` e `Model`
- Indicadores adicionais no relatorio
- Sugestao de login apos cadastro

## Proximos passos recomendados

- Adicionar modulo de receitas
- Separar relatorio por periodo e categoria
- Criar exportacao em PDF
- Melhorar autenticacao com recuperacao de senha
- Adicionar pagina de configuracoes do usuario

## Observacao para TCC

O projeto ja cobre uma base solida para demonstracao academica: CRUD completo, seguranca basica, dashboard, relatórios, exportacao e uma interface visual consistente.
