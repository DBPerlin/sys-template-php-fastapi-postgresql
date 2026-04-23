-- Seed de desenvolvimento: admin de teste
-- NUNCA aplicar em produção. Este arquivo é montado apenas pelo docker-compose.dev.yml.
--
-- Credenciais:
--   login: admin
--   senha: admin123  (MD5 = 0192023a7bbd73250516f069df18b500)

\connect db_core;

INSERT INTO membro (id, nome, id_tipo)
VALUES (1, 'Admin Dev', 0)
ON CONFLICT (id) DO NOTHING;

INSERT INTO usuario (id_membro, nome, senha_hash, ativo)
VALUES (1, 'admin', '0192023a7bbd73250516f069df18b500', TRUE)
ON CONFLICT (nome) DO NOTHING;

SELECT setval(pg_get_serial_sequence('membro', 'id'),  COALESCE((SELECT MAX(id) FROM membro),  1));
SELECT setval(pg_get_serial_sequence('usuario', 'id'), COALESCE((SELECT MAX(id) FROM usuario), 1));
