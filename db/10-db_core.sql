-- Schema do db_core
-- Tabelas de usuários/membros usadas pelo endpoint /core/validar_usuario
--
-- Em produção, aplicar contra o database db_core do CFN-BANCO:
--   psql -h 172.31.5.194 -U vivencias_admin -d db_core -f init_db_core.sql
--
-- Em dev (override docker-compose.dev.yml), o Postgres local roda este arquivo
-- automaticamente via /docker-entrypoint-initdb.d/.

\connect db_core;

CREATE TABLE IF NOT EXISTS membro (
    id       SERIAL PRIMARY KEY,
    nome     VARCHAR(255) NOT NULL,
    id_tipo  INTEGER NOT NULL DEFAULT 0,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS usuario (
    id          SERIAL PRIMARY KEY,
    id_membro   INTEGER NOT NULL REFERENCES membro(id) ON DELETE CASCADE,
    nome        VARCHAR(100) NOT NULL UNIQUE,
    senha_hash  VARCHAR(64) NOT NULL,
    ativo       BOOLEAN NOT NULL DEFAULT TRUE,
    criado_em   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_usuario_nome ON usuario(nome);
