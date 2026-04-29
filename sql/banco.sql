-- Banco de Dados GranaFlow
-- Estrutura completa do banco de dados

CREATE DATABASE IF NOT EXISTS granaflow;
USE granaflow;

-- ============================================
-- TABELAS DE CONFIGURAÇÃO
-- ============================================

CREATE TABLE IF NOT EXISTS moedas (
  id_moeda INT AUTO_INCREMENT PRIMARY KEY,
  codigo VARCHAR(10) NOT NULL UNIQUE,
  nome VARCHAR(50) NOT NULL,
  simbolo VARCHAR(5) NOT NULL
);

INSERT INTO moedas (codigo, nome, simbolo) VALUES
('BRL', 'Real Brasileiro', 'R$'),
('USD', 'Dólar Americano', '$'),
('EUR', 'Euro', '€');

-- ============================================
-- TABELAS DE USUÁRIOS
-- ============================================

CREATE TABLE IF NOT EXISTS usuarios (
  id_usuario INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(30) NOT NULL,
  email VARCHAR(50) NOT NULL UNIQUE,
  senha VARCHAR(255) NOT NULL,
  id_moeda INT DEFAULT 1,
  data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
  data_atualizacao DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (id_moeda) REFERENCES moedas(id_moeda)
);

-- ============================================
-- TABELAS DE CATEGORIAS
-- ============================================

CREATE TABLE IF NOT EXISTS tipos_categoria (
  id_tipo INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(20) NOT NULL UNIQUE
);

INSERT INTO tipos_categoria (nome) VALUES
('Receita'),
('Despesa');

CREATE TABLE IF NOT EXISTS categorias (
  id_categoria INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(30) NOT NULL,
  id_tipo INT NOT NULL,
  id_usuario INT NULL,
  FOREIGN KEY (id_tipo) REFERENCES tipos_categoria(id_tipo),
  FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
);

INSERT INTO categorias (nome, id_tipo) VALUES
('🍔 Alimentação', 2),
('🚗 Transporte', 2),
('⚕️ Saúde', 2),
('📚 Educação', 2),
('🎬 Entretenimento', 2),
('🛍️ Compras', 2),
('💡 Contas', 2);

-- ============================================
-- TABELAS DE TRANSAÇÕES
-- ============================================

CREATE TABLE IF NOT EXISTS gastos (
  id_gasto INT AUTO_INCREMENT PRIMARY KEY,
  id_usuario INT NOT NULL,
  id_categoria INT NOT NULL,
  id_moeda INT NOT NULL DEFAULT 1,
  descricao VARCHAR(255),
  valor DECIMAL(10,2) NOT NULL,
  data_gasto DATE NOT NULL,
  data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
  data_atualizacao DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
  FOREIGN KEY (id_categoria) REFERENCES categorias(id_categoria) ON DELETE CASCADE,
  FOREIGN KEY (id_moeda) REFERENCES moedas(id_moeda)
);

CREATE TABLE IF NOT EXISTS receitas (
  id_receita INT AUTO_INCREMENT PRIMARY KEY,
  id_usuario INT NOT NULL,
  id_moeda INT NOT NULL DEFAULT 1,
  descricao VARCHAR(255),
  valor DECIMAL(10,2) NOT NULL,
  data_receita DATE NOT NULL,
  data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
  FOREIGN KEY (id_moeda) REFERENCES moedas(id_moeda)
);

-- ============================================
-- TABELAS DE METAS
-- ============================================

CREATE TABLE IF NOT EXISTS metas (
  id_meta INT AUTO_INCREMENT PRIMARY KEY,
  id_usuario INT NOT NULL,
  id_categoria INT NULL,
  nome_meta VARCHAR(50) NOT NULL,
  valor_limite DECIMAL(10,2) NOT NULL,
  data_inicio DATE,
  data_fim DATE,
  data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
  FOREIGN KEY (id_categoria) REFERENCES categorias(id_categoria) ON DELETE SET NULL
);

-- ============================================
-- ÍNDICES PARA OTIMIZAÇÃO
-- ============================================

CREATE INDEX IF NOT EXISTS idx_gastos_usuario ON gastos(id_usuario);
CREATE INDEX IF NOT EXISTS idx_gastos_categoria ON gastos(id_categoria);
CREATE INDEX IF NOT EXISTS idx_receitas_usuario ON receitas(id_usuario);
CREATE INDEX IF NOT EXISTS idx_metas_usuario ON metas(id_usuario);
