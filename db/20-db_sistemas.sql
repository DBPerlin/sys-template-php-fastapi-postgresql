-- Schema do db_sistemas
-- Tabelas das experiências do Vivências em Nutrição
--
-- Em produção, aplicar contra o database db_sistemas do CFN-BANCO:
--   psql -h 172.31.5.194 -U vivencias_admin -d db_sistemas -f init_db_sistemas.sql

\connect db_sistemas;

CREATE TABLE IF NOT EXISTS vivencias_experiencia (
    id                    SERIAL PRIMARY KEY,
    crn_id                INTEGER NOT NULL,
    nome_completo         VARCHAR(255) NOT NULL,
    cpf                   VARCHAR(64) NOT NULL,
    inscricao             VARCHAR(50) NOT NULL,
    estado_id             INTEGER NOT NULL,
    titulo_trabalho       VARCHAR(255) NOT NULL,
    area_nutricao         VARCHAR(100) NOT NULL,
    telefone              VARCHAR(30) NOT NULL,
    email                 VARCHAR(255) NOT NULL,
    objetivo_trabalho     TEXT,
    acoes_trabalho        TEXT,
    resultados_trabalho   TEXT,
    status                SMALLINT NOT NULL DEFAULT 0,
    criado_em             TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em         TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

COMMENT ON COLUMN vivencias_experiencia.status IS '0=pendente, 1=aprovado, 2=rejeitado';
COMMENT ON COLUMN vivencias_experiencia.cpf IS 'HMAC-SHA256 do CPF (chave CPF_HASH_SECRET)';

CREATE INDEX IF NOT EXISTS idx_vivencias_experiencia_crn    ON vivencias_experiencia(crn_id);
CREATE INDEX IF NOT EXISTS idx_vivencias_experiencia_status ON vivencias_experiencia(status);

CREATE TABLE IF NOT EXISTS vivencias_experiencia_arquivo (
    id                       SERIAL PRIMARY KEY,
    id_vivencia_experiencia  INTEGER NOT NULL REFERENCES vivencias_experiencia(id) ON DELETE CASCADE,
    nome_original            VARCHAR(255) NOT NULL,
    nome_arquivo             VARCHAR(255) NOT NULL,
    caminho_arquivo          VARCHAR(500) NOT NULL,
    content_type             VARCHAR(100) NOT NULL,
    tamanho_bytes            BIGINT NOT NULL,
    criado_em                TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_vivencias_arquivo_experiencia
    ON vivencias_experiencia_arquivo(id_vivencia_experiencia);

CREATE TABLE IF NOT EXISTS vivencias_experiencia_status_historico (
    id                       SERIAL PRIMARY KEY,
    id_vivencia_experiencia  INTEGER NOT NULL REFERENCES vivencias_experiencia(id) ON DELETE CASCADE,
    status_anterior          SMALLINT NOT NULL,
    status_novo              SMALLINT NOT NULL,
    criado_em                TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_vivencias_status_historico_experiencia
    ON vivencias_experiencia_status_historico(id_vivencia_experiencia);
