-- Flyway Migration V3 — Evolução do schema
-- GCS 2026/A — Demonstra versionamento do banco

ALTER TABLE receita
    ADD COLUMN IF NOT EXISTS porcoes   INT            DEFAULT 4     COMMENT 'Número de porções',
    ADD COLUMN IF NOT EXISTS tempo_min INT            DEFAULT 30    COMMENT 'Tempo de preparo em minutos',
    ADD COLUMN IF NOT EXISTS ativo     TINYINT(1)     DEFAULT 1     COMMENT 'Receita ativa?',
    ADD COLUMN IF NOT EXISTS atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Adicionar índice para buscas por tipo
CREATE INDEX IF NOT EXISTS idx_tipo_receita ON receita(tipo_receita);
CREATE INDEX IF NOT EXISTS idx_data_registro ON receita(data_registro);
