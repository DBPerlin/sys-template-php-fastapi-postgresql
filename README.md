# Vivências em Nutrição

Sistema de cadastro e moderação de **Experiências Exitosas na Nutrição**, desenvolvido a pedido da Comissão de Comunicação do CFN.

**Produção:** https://vivencias.cfn.org.br
**Porta interna:** 8012
**Stack:** PHP 7.4 (frontend) + FastAPI/Python 3.11 (backend) + PostgreSQL + MySQL (consulta-profissional)

---

## Arquitetura

```
 ┌────────────┐           ┌─────────────┐          ┌────────────────────┐
 │  Browser   │  HTTPS    │  web (PHP)  │  rede    │  backend (FastAPI) │
 │            │ ────────► │  Apache     │ ───────► │  uvicorn :8000     │
 │            │           │  + mod_proxy│          │                    │
 └────────────┘           └─────────────┘          └────────┬───────────┘
                                /api/* encaminhado          │
                                                            │
                                ┌───────────────────────────┤
                                │                           │
                        ┌───────▼──────┐           ┌────────▼────────┐
                        │ PostgreSQL   │           │ MySQL           │
                        │ db_core      │           │ db_list_        │
                        │ db_sistemas  │           │ nutricionistas  │
                        │ (CFN-BANCO)  │           │ (CFN-BANCO)     │
                        └──────────────┘           └─────────────────┘
```

- **Frontend PHP** (`web/`) serve a landing, formulários e área admin. Apache com `mod_proxy` expõe `/api/*` apontando para o backend interno.
- **Backend FastAPI** (`backend/`) concentra as rotas `/core/*` e `/vivencias/*`. Não é exposto diretamente ao ALB — só responde dentro da rede Docker.
- **Postgres** guarda dados de login (`db_core`) e das experiências (`db_sistemas`).
- **MySQL** é consultado apenas para validar CRN/CPF no cadastro (tabela `nutricionista_implanta` / `nutricionista_incorp`).

---

## Endpoints (backend)

| Método | Rota | Descrição |
|---|---|---|
| `POST` | `/core/validar_usuario` | Login do admin (nome + senha MD5) |
| `POST` | `/core/cadastrar_usuario` | Cria usuário admin — **requer header `X-Admin-Secret`** (ver `.env`) |
| `GET`  | `/core/consulta-profissional` | Valida nutricionista (CRN + CPF) ⚠️ migrar p/ api-cfn |
| `POST` | `/vivencias/nova_experiencia` | Cadastra experiência (multipart com arquivo) |
| `POST` | `/vivencias/alterar_status` | Moderar (0 pendente / 1 aprovado / 2 rejeitado) |
| `GET`  | `/vivencias/lista_experiencias` | Lista tudo (admin) |
| `GET`  | `/vivencias/lista_experiencias/{crn_id}` | Lista por CRN |
| `GET`  | `/vivencias/arquivo_experiencia/{id}` | Download do PDF/imagem anexado |

Via Apache, o frontend chama esses endpoints como `/api/core/...` e `/api/vivencias/...`.

---

## Desenvolvimento local

```bash
cp .env.example .env           # (os valores são ignorados em dev, mas o arquivo precisa existir)
docker compose -f docker-compose.yml -f docker-compose.dev.yml up -d --build
```

Isso sobe:
- `vivencias-web` (porta **8012** no host)
- `vivencias-backend` (interno)
- `vivencias-postgres-dev` (porta **5442** no host, para inspecionar com psql)
- `vivencias-mysql-cnn-dev` (porta **3317** no host)

Schemas Postgres são aplicados automaticamente via `/docker-entrypoint-initdb.d/` (arquivos em `db/`).

### Seed de desenvolvimento

Ao subir o Postgres pela primeira vez (volume vazio), o arquivo `db/dev-seed/90-admin.sql` cria um usuário admin para teste:

- **Login:** `admin`
- **Senha:** `admin123`

O mount do seed está **apenas** no `docker-compose.dev.yml` — em produção esse arquivo nunca é aplicado.

Para resetar o banco e rodar o seed de novo:
```bash
docker compose -f docker-compose.yml -f docker-compose.dev.yml down
docker volume rm vivencias_vivencias-postgres-data
docker compose -f docker-compose.yml -f docker-compose.dev.yml up -d
```

Acesse: http://localhost:8012

### Cadastrar um admin manualmente (produção)

Enquanto não há painel admin para criação de usuários, rode de dentro do container do backend:

```bash
ssh ubuntu@18.119.250.163
docker exec -it vivencias-backend bash

# Dentro do container — o ADMIN_API_SECRET já está no ambiente
curl -s -X POST http://localhost:8000/core/cadastrar_usuario \
  -H "Content-Type: application/json" \
  -H "X-Admin-Secret: $ADMIN_API_SECRET" \
  -d '{"nome":"fulano","senha":"senha_escolhida"}'
```

A rota está **protegida por header secreto** (`X-Admin-Secret`) e só aceita chamadas com o segredo correto. Quando um painel admin for adicionado no frontend, o PHP enviará o header server-side (o segredo nunca toca o browser).

---

## Produção (CFN-APPS)

1. Criar databases e usuário no CFN-BANCO:
   ```bash
   # Postgres (como postgres superuser)
   psql -h 172.31.5.194 -U postgres -f db/00-databases.sql
   psql -h 172.31.5.194 -U vivencias_admin -d db_core      -f db/10-db_core.sql
   psql -h 172.31.5.194 -U vivencias_admin -d db_sistemas  -f db/20-db_sistemas.sql

   # MySQL (usuário readonly para consulta-profissional)
   mysql -h 172.31.5.194 -u root -p -e "CREATE USER 'cnn_readonly'@'172.31.4.182' IDENTIFIED WITH mysql_native_password BY 'SENHA'; GRANT SELECT ON db_list_nutricionistas.* TO 'cnn_readonly'@'172.31.4.182';"
   ```

2. Na EC2 CFN-APPS:
   ```bash
   cd /data/apps
   git clone https://gitlab.cfn.org.br/fellipe.rocha/vivencias.git
   cd vivencias
   cp .env.example .env
   # editar .env com senhas reais
   docker compose up -d --build
   ```

3. No ALB, criar target group apontando para CFN-APPS:8012 e regra de host `vivencias.cfn.org.br`.
4. No Cloudflare, CNAME `vivencias` → ALB.

---

## Estrutura do repo

```
vivencias/
├── web/                  # frontend PHP (Apache + mod_proxy)
│   ├── Dockerfile
│   ├── apache-vivencias.conf
│   └── app/              # index.php, admin.php, form.php, interna.php, assets/...
├── backend/              # FastAPI
│   ├── Dockerfile
│   ├── requirements.txt
│   └── app/
│       ├── main.py
│       ├── db_core.py
│       ├── db_sistemas.py
│       ├── db_cnn.py
│       ├── core/         # validar_usuario + consulta-profissional
│       └── vivencias/    # nova_exp + router
├── db/                   # scripts SQL (aplicados auto em dev)
│   ├── 00-databases.sql
│   ├── 10-db_core.sql
│   ├── 20-db_sistemas.sql
│   └── dev-seed/         # seeds só de dev (nunca aplicados em prod)
│       └── 90-admin.sql  # cria admin/admin123 no db_core
├── docker-compose.yml        # produção (aponta pro CFN-BANCO via .env)
├── docker-compose.dev.yml    # override: Postgres + MySQL locais
├── .env.example
├── .gitlab-ci.yml            # deploy auto na main (tag cfn-apps)
└── TODO.md
```
