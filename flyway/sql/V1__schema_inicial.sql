-- Flyway Migration V1 — Schema inicial
-- GCS 2026/A

CREATE DATABASE IF NOT EXISTS db_receitas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_receitas;

CREATE TABLE IF NOT EXISTS usuario (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    nome       VARCHAR(100) NOT NULL,
    login      VARCHAR(50)  NOT NULL UNIQUE,
    senha      VARCHAR(255) NOT NULL,
    situacao   ENUM('ativo', 'inativo') NOT NULL DEFAULT 'ativo',
    criado_em  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS receita (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    nome           VARCHAR(150) NOT NULL,
    descricao      TEXT,
    data_registro  DATE NOT NULL,
    custo          DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    tipo_receita   ENUM('doce', 'salgada') NOT NULL,
    criado_em      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
