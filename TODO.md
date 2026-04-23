# TODO — vivências

## Estrutura inicial
- [x] Reorganizar frontend para `web/app/`
- [x] Backend FastAPI (core + vivencias) em `backend/`
- [x] `docker-compose.yml` (prod) + `docker-compose.dev.yml` (Postgres + MySQL locais)
- [x] Schemas SQL (`db/00-databases.sql`, `db/10-db_core.sql`, `db/20-db_sistemas.sql`)
- [x] `.env.example`
- [x] `README.md`
- [x] `.gitlab-ci.yml` (tag `cfn-apps`, deploy na main)

## Testes locais
- [ ] Subir `docker compose -f docker-compose.yml -f docker-compose.dev.yml up -d --build`
- [ ] Conferir que schemas Postgres foram aplicados
- [ ] Testar `/` do backend (healthcheck)
- [ ] Cadastrar usuário admin: `POST /api/core/cadastrar_usuario`
- [ ] Fazer login em `/admin.php` e validar redirect para `interna.php`
- [ ] Testar cadastro de experiência em `/form.php` (com e sem anexo)
- [ ] Testar moderação (aprovar/rejeitar) em `interna.php`
- [ ] Testar download do arquivo anexado
- [ ] Testar `consulta-profissional` com dados reais de `db_list_nutricionistas`

## Produção (AWS)
- [x] Instalar PostgreSQL 16 no CFN-BANCO (ao lado do MySQL)
- [x] Criar databases `db_core` e `db_sistemas` no CFN-BANCO
- [x] Criar usuário `vivencias_admin` no Postgres com senha forte
- [ ] Aplicar schemas (10-db_core.sql, 20-db_sistemas.sql) no CFN-BANCO
- [ ] Criar usuário MySQL `cnn_readonly` no CFN-BANCO com SELECT em `db_list_nutricionistas` + grant do IP da CFN-APPS
- [ ] Criar repo GitLab `fellipe.rocha/vivencias`
- [ ] Fazer push da estrutura inicial
- [ ] Clonar em `/data/apps/vivencias` na EC2 CFN-APPS
- [ ] Criar `.env` de produção com senhas reais + `CPF_HASH_SECRET` gerado
- [ ] `docker compose up -d --build`
- [ ] Cadastrar primeiro usuário admin via backend (um-shot)
- [ ] Criar target group apontando para CFN-APPS:8012
- [ ] Adicionar regra no ALB: Host header `vivencias.cfn.org.br` → target group
- [ ] Criar CNAME no Cloudflare: `vivencias.cfn.org.br` → ALB
- [ ] Validar fluxo end-to-end em produção

## Dívidas técnicas / melhorias futuras
- [ ] **Autenticação:** hoje a sessão é apenas um booleano (`$_SESSION["token"] = "autenticado"`) porque o backend não emite JWT. Implementar JWT com expiração e trocar o check nos PHPs.
- [ ] **Migrar `/core/consulta-profissional` para api-cfn.** Esse endpoint é transversal — pertence naturalmente à API pública do CFN, não a este projeto. Quando migrar, remover de `backend/app/core/consulta_profissional.py`, `db_cnn.py`, variáveis `CNN_DB_*` do `.env`, e trocar no frontend `enviar_valida_profissional.php` para chamar `api.cfn.org.br`.
- [ ] **Remover senha MD5 do login** (`db_core.usuario.senha_hash`) — migrar para `bcrypt`/`argon2`.
- [ ] **Campos `objetivo_trabalho`, `acoes_trabalho`, `resultados_trabalho`** estão em `vivencias_experiencia` mas o README original mencionava campo único `relato`. Confirmar com a área se o modelo atual está correto.
- [ ] **consulta-profissional assume schema do `nutricionista_implanta`/`nutricionista_incorp`** do CNN. Se o schema real diferir, ajustar `backend/app/core/consulta_profissional.py`.
- [ ] **Backup do Postgres do CFN-BANCO:** AWS Backup cobre o volume, mas adicionar cron com `pg_dump` lógico diário para `/data/backups/postgres/`.
