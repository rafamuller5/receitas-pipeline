-- ============================================
-- SISTEMA DE RECEITAS - Script do Banco de Dados
-- Banco: MySQL
-- ============================================

CREATE DATABASE IF NOT EXISTS db_receitas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_receitas;

-- Tabela: usuario
CREATE TABLE IF NOT EXISTS usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    login VARCHAR(50) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    situacao ENUM('ativo', 'inativo') NOT NULL DEFAULT 'ativo',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela: receita
CREATE TABLE IF NOT EXISTS receita (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    descricao TEXT,
    data_registro DATE NOT NULL,
    custo DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    tipo_receita ENUM('doce', 'salgada') NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- POPULAR: Usuários (senhas: admin123 / maria123)
-- ============================================
INSERT INTO usuario (nome, login, senha, situacao) VALUES
('Administrador', 'admin', MD5('admin123'), 'ativo'),
('Maria Silva', 'maria', MD5('maria123'), 'ativo');

-- ============================================
-- POPULAR: 10 Receitas
-- ============================================
INSERT INTO receita (nome, descricao, data_registro, custo, tipo_receita) VALUES
('Bolo de Chocolate', 'Bolo fofinho de chocolate com cobertura de ganache. Leva farinha, ovos, açúcar, cacau em pó, leite e manteiga.', '2024-01-10', 35.50, 'doce'),
('Coxinha de Frango', 'Coxinha tradicional com massa de batata e recheio de frango desfiado temperado com cheiro-verde.', '2024-01-15', 48.00, 'salgada'),
('Brigadeiro Gourmet', 'Brigadeiro cremoso feito com chocolate belga, leite condensado e manteiga sem sal. Rendimento: 40 unidades.', '2024-02-03', 22.00, 'doce'),
('Esfiha Aberta', 'Esfiha de carne moída temperada com cebola, tomate, pimentão e especiarias árabes.', '2024-02-14', 55.00, 'salgada'),
('Pudim de Leite', 'Pudim clássico com calda de caramelo. Ingredientes: leite condensado, leite integral e ovos.', '2024-03-01', 18.50, 'doce'),
('Empadão de Frango', 'Empadão cremoso com recheio de frango, requeijão e azeitonas. Massa amanteigada crocante.', '2024-03-22', 62.00, 'salgada'),
('Mousse de Maracujá', 'Mousse leve e aerada de maracujá com creme de leite e leite condensado. Serve 8 porções.', '2024-04-05', 28.00, 'doce'),
('Quiche de Queijo', 'Quiche lorraine com queijo gruyère, bacon e creme de leite fresco. Massa podre crocante.', '2024-04-18', 45.00, 'salgada'),
('Torta de Limão', 'Torta refrescante com creme de limão siciliano e merengue italiano tostado.', '2024-05-10', 32.00, 'doce'),
('Pastel de Forno', 'Pastel de forno recheado com carne moída e queijo, assado até dourar. Massa macia e levemente folhada.', '2024-05-28', 40.00, 'salgada');
