-- Banco de Dados GranaFlow - VERSÃO CORRIGIDA
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO moedas (id_moeda, codigo, nome, simbolo) VALUES
(1, 'BRL', 'Real Brasileiro', 'R$'),
(2, 'USD', 'Dólar Americano', '$'),
(3, 'EUR', 'Euro', '€');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABELAS DE CATEGORIAS
-- ============================================

CREATE TABLE IF NOT EXISTS tipos_categoria (
  id_tipo INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(20) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO tipos_categoria (id_tipo, nome) VALUES
(1, 'Receita'),
(2, 'Despesa');

CREATE TABLE IF NOT EXISTS categorias (
  id_categoria INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(30) NOT NULL,
  id_tipo INT NOT NULL,
  id_usuario INT NULL,
  FOREIGN KEY (id_tipo) REFERENCES tipos_categoria(id_tipo),
  FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO categorias (nome, id_tipo) VALUES
('Alimentação', 2),
('Transporte', 2),
('Saúde', 2),
('Educação', 2),
('Entretenimento', 2),
('Compras', 2),
('Contas', 2);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABELA DE SALÁRIOS (Faltava no original)
-- ============================================

CREATE TABLE IF NOT EXISTS salarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_usuario INT NOT NULL,
  valor DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  data_atualizacao DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABELA DE GASTOS RECORRENTES (Faltava no original)
-- ============================================

CREATE TABLE IF NOT EXISTS gastos_recorrentes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_usuario INT NOT NULL,
  id_categoria INT NOT NULL,
  descricao VARCHAR(255),
  valor DECIMAL(10,2) NOT NULL,
  dia_vencimento INT NOT NULL,
  tipo ENUM('mensal', 'parcelado') DEFAULT 'mensal',
  quantidade_meses INT DEFAULT NULL,
  ativo TINYINT(1) DEFAULT 1,
  ultima_execucao DATE DEFAULT NULL,
  data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
  FOREIGN KEY (id_categoria) REFERENCES categorias(id_categoria) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABELAS DE METAS (Corrigida com colunas tipo e valor_guardado)
-- ============================================

CREATE TABLE IF NOT EXISTS metas (
  id_meta INT AUTO_INCREMENT PRIMARY KEY,
  id_usuario INT NOT NULL,
  id_categoria INT NULL,
  nome_meta VARCHAR(50) NOT NULL,
  valor_limite DECIMAL(10,2) NOT NULL,
  valor_guardado DECIMAL(10,2) DEFAULT 0.00,
  tipo ENUM('gasto', 'reserva') DEFAULT 'gasto',
  data_inicio DATE,
  data_fim DATE,
  data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
  FOREIGN KEY (id_categoria) REFERENCES categorias(id_categoria) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- ÍNDICES PARA OTIMIZAÇÃO
-- ============================================

CREATE INDEX idx_gastos_usuario ON gastos(id_usuario);
CREATE INDEX idx_gastos_categoria ON gastos(id_categoria);
CREATE INDEX idx_receitas_usuario ON receitas(id_usuario);
CREATE INDEX idx_metas_usuario ON metas(id_usuario);

-- TABELA DE DINHEIRO GUARDADO (poupança avulsa)
CREATE TABLE IF NOT EXISTS dinheiro_guardado (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_usuario INT NOT NULL,
  valor DECIMAL(10,2) NOT NULL,
  descricao VARCHAR(255) DEFAULT 'Dinheiro guardado',
  data_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
);
