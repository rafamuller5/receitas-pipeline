-- Flyway Migration V4 - Cria tabela categoria
-- GCS 2026/A - Nova tabela auxiliar, apenas estrutura de banco (sem uso no código ainda)

CREATE TABLE IF NOT EXISTS categoria (
    id INT AUTO_INCREMENT PRIMARY KEY,
    descricao VARCHAR(100) NOT NULL
);
