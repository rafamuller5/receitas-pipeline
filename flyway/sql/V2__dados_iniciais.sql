-- Flyway Migration V2 — Dados iniciais
-- GCS 2026/A

INSERT IGNORE INTO usuario (nome, login, senha, situacao) VALUES
('Administrador', 'admin', MD5('admin123'), 'ativo'),
('Maria Silva',   'maria', MD5('maria123'), 'ativo');

INSERT IGNORE INTO receita (nome, descricao, data_registro, custo, tipo_receita) VALUES
('Bolo de Chocolate',  'Bolo fofinho de chocolate com cobertura de ganache.',            '2024-01-10', 35.50, 'doce'),
('Coxinha de Frango',  'Coxinha tradicional com massa de batata.',                       '2024-01-15', 48.00, 'salgada'),
('Brigadeiro Gourmet', 'Brigadeiro cremoso com chocolate belga.',                        '2024-02-03', 22.00, 'doce'),
('Esfiha Aberta',      'Esfiha de carne moída temperada com especiarias árabes.',        '2024-02-14', 55.00, 'salgada'),
('Pudim de Leite',     'Pudim clássico com calda de caramelo.',                          '2024-03-01', 18.50, 'doce');
