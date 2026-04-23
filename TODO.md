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
- [x] Aplicar schemas (10-db_core.sql, 20-db_sistemas.sql) no CFN-BANCO
- [x] Migrar dados do prod-core (18 membros, 3 usuários, 23 experiências, 4 históricos) — arquivos físicos perdidos, `vivencias_experiencia_arquivo` ficou sem dados
- [x] Criar usuário MySQL `cnn_readonly` no CFN-BANCO com SELECT em `db_list_nutricionistas` + grant do IP da CFN-APPS
- [x] Criar repo GitLab `fellipe.rocha/vivencias`
- [x] Fazer push da estrutura inicial
- [x] Clonar em `/data/apps/vivencias` na EC2 CFN-APPS
- [x] Criar `.env` de produção com senhas reais + `CPF_HASH_SECRET` gerado
- [x] `docker compose up -d --build`
- [x] Resetar senha do `fellipe.rocha` no Postgres (prod-core usava LDAP, hash migrado era placeholder)
- [x] ~~Cadastrar primeiro usuário admin via backend (um-shot)~~ — não necessário, 3 admins vieram do prod-core (admin, usuario, fellipe.rocha)
- [x] Criar target group apontando para CFN-APPS:8012
- [x] Adicionar regra no ALB: Host header `vivencias.cfn.org.br` → target group (priority 38)
- [x] Criar CNAME no Cloudflare: `vivencias.cfn.org.br` → ALB
- [x] Validar fluxo end-to-end em produção (2026-04-23)
- [x] Limpar 4 relatos de teste da base (IDs 1-4) via `DELETE FROM vivencias_experiencia WHERE id BETWEEN 1 AND 4`

## Dívidas técnicas / melhorias futuras
- [ ] **Delete de experiência:** hoje não há endpoint para apagar relatos — é necessário rodar SQL direto no banco. Adicionar `DELETE /vivencias/experiencia/{id}` no backend (FKs já têm `ON DELETE CASCADE`) e coordenar com o dev do frontend para plugar botão na tela admin.
- [ ] **Autenticação:** hoje a sessão é apenas um booleano (`$_SESSION["token"] = "autenticado"`) porque o backend não emite JWT. Implementar JWT com expiração e trocar o check nos PHPs.
- [ ] **Migrar `/core/consulta-profissional` para api-cfn.** Esse endpoint é transversal — pertence naturalmente à API pública do CFN, não a este projeto. Quando migrar, remover de `backend/app/core/consulta_profissional.py`, `db_cnn.py`, variáveis `CNN_DB_*` do `.env`, e trocar no frontend `enviar_valida_profissional.php` para chamar `api.cfn.org.br`.
- [ ] **Autenticação LDAP (paridade com prod-core):** no prod-core, login dos admins era via LDAP; os `senha_hash` migrados não representam as senhas reais, então os admins precisaram ter a senha resetada manualmente no Postgres. Integrar LDAP no backend FastAPI (ou delegar para o api-cfn com endpoint de auth compartilhado) e remover a necessidade de senha local.
- [ ] **Remover senha MD5 do login** (`db_core.usuario.senha_hash`) — migrar para `bcrypt`/`argon2`. Coordenar com a implementação do LDAP acima.
- [ ] **Campos `objetivo_trabalho`, `acoes_trabalho`, `resultados_trabalho`** estão em `vivencias_experiencia` mas o README original mencionava campo único `relato`. Confirmar com a área se o modelo atual está correto.
- [ ] **consulta-profissional assume schema do `nutricionista_implanta`/`nutricionista_incorp`** do CNN. Se o schema real diferir, ajustar `backend/app/core/consulta_profissional.py`.
- [ ] **Backup do Postgres do CFN-BANCO:** AWS Backup cobre o volume, mas adicionar cron com `pg_dump` lógico diário para `/data/backups/postgres/`.
