-- Cria os databases e o owner usados pelo Vivências.
-- Em dev, este script roda contra o POSTGRES_DB default (postgres), como superuser.
-- Em produção, rodar manualmente conectado como superuser no CFN-BANCO.

CREATE DATABASE db_core;
CREATE DATABASE db_sistemas;
