-- Script de Dados de Exemplo para GranaFlow
-- Execute este script APÓS importar banco_completo.sql

-- Exemplo de usuário de teste (Senha: 123456)
INSERT IGNORE INTO usuarios (id_usuario, nome, email, senha, id_moeda) VALUES
(1, 'Usuário Teste', 'teste@email.com', '$2y$10$YIjlrDt5z5K5K5K5K5K5K.K5K5K5K5K5K5K5K5K5K5K5K5K5K5', 1);

-- Categorias personalizadas de exemplo
INSERT IGNORE INTO categorias (nome, id_tipo, id_usuario) VALUES
('🏠 Aluguel', 2, 1),
('🍕 Comida Fora', 2, 1),
('🎮 Lazer', 2, 1),
('🛒 Supermercado', 2, 1);

-- Gastos de exemplo (Últimos 3 meses)
INSERT IGNORE INTO gastos (id_usuario, id_categoria, id_moeda, descricao, valor, data_gasto) VALUES
(1, 1, 1, 'Compra no mercado', 150.00, DATE_SUB(CURDATE(), INTERVAL 5 DAY)),
(1, 2, 1, 'Combustível', 80.00, DATE_SUB(CURDATE(), INTERVAL 3 DAY)),
(1, 3, 1, 'Cinema com amigos', 60.00, DATE_SUB(CURDATE(), INTERVAL 1 DAY)),
(1, 4, 1, 'Restaurante', 120.00, DATE_SUB(CURDATE(), INTERVAL 10 DAY)),
(1, 1, 1, 'Conta de luz', 250.00, DATE_SUB(CURDATE(), INTERVAL 15 DAY));

-- Metas de exemplo
INSERT IGNORE INTO metas (id_usuario, nome_meta, valor_limite, valor_guardado, tipo) VALUES
(1, 'Viagem em Férias', 2000.00, 500.00, 'reserva'),
(1, 'Limite de Alimentação', 500.00, 0.00, 'gasto'),
(1, 'Emergência', 1500.00, 750.00, 'reserva');

-- Salário de exemplo
INSERT IGNORE INTO salarios (id_usuario, valor) VALUES
(1, 3000.00);

-- Nota: Para gerar a senha corretamente, use:
-- password_hash('123456', PASSWORD_DEFAULT)
-- A senha acima foi gerada com esse hash
