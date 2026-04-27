# 🧳 desafio-rootcode-api — Backend Laravel 12

> **Sistema de Gerenciamento de Tarefas Internas** para uma Agência de Viagens.
> API RESTful construída com **Laravel 12** + **MySQL** + **Laravel Sanctum**.

---

## 📋 Índice

1. [O que é este projeto?](#-o-que-é-este-projeto)
2. [Fluxo geral do sistema](#-fluxo-geral-do-sistema)
3. [Pré-requisitos](#-pré-requisitos)
4. [Instalação passo a passo](#-instalação-passo-a-passo)
5. [Estrutura de pastas](#-estrutura-de-pastas)
6. [Banco de dados — tabelas e relações](#-banco-de-dados--tabelas-e-relações)
7. [Controllers — o que cada um faz](#-controllers--o-que-cada-um-faz)
8. [Models — representação dos dados](#-models--representação-dos-dados)
9. [Rotas da API](#-rotas-da-api)
10. [Regras de negócio](#-regras-de-negócio)
11. [Dados de teste](#-dados-de-teste)
12. [Comandos úteis](#-comandos-úteis)
13. [Arquivos padrão do Laravel](#-arquivos-padrão-do-laravel-não-customizados)

---

## 🗺️ O que é este projeto?

Este é o **backend** de um sistema de gerenciamento de tarefas desenvolvido para uma agência de viagens. Ele funciona como uma **API REST** — ou seja, não tem interface visual própria. Ele recebe requisições do frontend (Next.js), processa os dados e retorna respostas em formato JSON.

Pense nele como um "garçom" entre o banco de dados e a tela do usuário:

```
  USUÁRIO                FRONTEND               BACKEND (este projeto)        BANCO
┌─────────┐           ┌──────────┐             ┌──────────────────────┐   ┌────────┐
│         │  clica    │ Next.js  │  envia req  │  Laravel API         │   │ MySQL  │
│Navegador│ ────────► │ :3000    │ ──────────► │  :8000               │──►│ :3306  │
│         │           │          │ ◄─────────  │  processa e responde │◄──│        │
└─────────┘           └──────────┘  JSON resp  └──────────────────────┘   └────────┘
```

---

## 🔄 Fluxo geral do sistema

### Fluxo de autenticação

```
  1. Frontend envia: { email: "...", password: "..." }
              │
              ▼
  2. AuthController::login() valida as credenciais
              │
      ┌───────┴────────┐
      │                │
  Válidas          Inválidas
      │                │
      ▼                ▼
  3. Gera token    Retorna erro 401
     Sanctum       "Credenciais inválidas"
      │
      ▼
  4. Frontend salva o token no localStorage
      │
      ▼
  5. Todas as próximas requisições enviam o token no header:
     Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc...
      │
      ▼
  6. Middleware auth:sanctum verifica o token
     antes de executar qualquer rota protegida
```

### Fluxo de uma requisição protegida

```
  Frontend: GET /api/tasks  (com token no header)
              │
              ▼
  routes/api.php  ──────►  Middleware auth:sanctum
                                    │
                            ┌───────┴────────┐
                            │                │
                        Token OK         Token inválido
                            │                │
                            ▼                ▼
                    TaskController        Retorna 401
                    ::index()
                            │
                            ▼
                    Task::orderBy('due_date')->get()
                            │
                            ▼
                    Retorna JSON com todas as tarefas
```

---

## ✅ Pré-requisitos

Antes de começar, você precisa ter instalado na sua máquina:

| Ferramenta | Versão mínima | Para que serve |
|------------|--------------|----------------|
| **PHP** | 8.3+ | Linguagem que executa o Laravel |
| **Composer** | 2.x | Gerenciador de pacotes do PHP (como o npm é para Node) |
| **MySQL** | 8.0+ | Banco de dados relacional |
| **Node.js** | 18+ | Necessário para alguns pacotes do Laravel |

### 🔍 Como verificar se já estão instalados

Abra o **Prompt de Comando (CMD)** ou **PowerShell** e execute:

```bash
php --version
# Esperado: PHP 8.2.x ou superior

composer --version
# Esperado: Composer version 2.x.x

mysql --version
# Esperado: mysql  Ver 8.x.x

node --version
# Esperado: v18.x.x ou superior
```

### 📥 Onde baixar (caso não tenha instalado)

- **PHP + MySQL**: [XAMPP](https://www.apachefriends.org/) — instala os dois juntos (recomendado para Windows) - baixe a versão com **PHP 8.3+**
- **Composer**: [getcomposer.org](https://getcomposer.org/download/)
- **Node.js**: [nodejs.org](https://nodejs.org/en/download/)

> 💡 Se usar o XAMPP, inicie o **Apache** e o **MySQL** pelo painel do XAMPP antes de continuar.

---

## 🚀 Instalação passo a passo

### Passo 1 — Clonar o repositório

```bash
# Navegue até onde quer salvar o projeto
cd C:\projetos

# Clone o repositório
git clone https://github.com/LucasFigueiredo1997/desafio-rootcode-api.git

# Entre na pasta
cd desafio-rootcode-api
```

### Passo 2 — Instalar as dependências PHP

```bash
# Baixa todas as bibliotecas listadas no composer.json
# (pode demorar alguns minutos na primeira vez)
composer install
```

> 💡 **Por que isso é necessário?** O arquivo `composer.json` lista todas as bibliotecas que o projeto usa (como o Sanctum, Fortify, etc.). O `composer install` lê essa lista e baixa tudo para a pasta `vendor/`. Essa pasta não vai para o GitHub — cada desenvolvedor precisa rodar esse comando localmente.

### Passo 3 — Criar e configurar o arquivo `.env`

```bash
# Copia o arquivo de exemplo
copy .env.example .env
```

Agora abra o `.env` com qualquer editor de texto (Notepad, VS Code) e edite as seguintes linhas:

```env
APP_NAME="Gerenciador de Tarefas"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000
APP_TIMEZONE=America/Sao_Paulo

# ⚠️ Mude DB_CONNECTION de sqlite para mysql e descomente as linhas abaixo:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=desafio_rootcode
DB_USERNAME=root
DB_PASSWORD=           # sua senha do MySQL (deixe vazio se não tiver)
```

> ⚠️ O arquivo `.env` **nunca deve ser enviado para o GitHub** pois contém informações sensíveis como senhas e chaves secretas.

### Passo 4 — Criar o banco de dados no MySQL

Abra o MySQL Workbench, HeidiSQL, ou o próprio CMD e execute:

```sql
CREATE DATABASE desafio_rootcode CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Ou pelo CMD (substitua `root` pelo seu usuário):
```bash
mysql -u root -p -e "CREATE DATABASE desafio_rootcode CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### Passo 5 — Gerar a chave da aplicação

```bash
php artisan key:generate
```

> 💡 O Laravel usa essa chave para criptografar dados sensíveis como tokens e cookies. Esse comando preenche automaticamente o campo `APP_KEY=` no seu `.env`.

### Passo 6 — Rodar as migrations (criar as tabelas)

```bash
php artisan migrate
```

> 💡 **O que são migrations?** São arquivos PHP que descrevem a estrutura do banco de dados. Quando você roda esse comando, o Laravel lê todos os arquivos da pasta `database/migrations/` em ordem cronológica e cria as tabelas automaticamente. É como uma receita de bolo para o banco de dados — cada desenvolvedor cria as mesmas tabelas rodando o mesmo comando.

### Passo 7 — Popular o banco com dados de teste

```bash
php artisan db:seed
```

> 💡 **O que são seeders?** São arquivos que inserem dados iniciais no banco. O `UserSeeder` cria 3 equipes, 10 gestores e 10 colaboradores automaticamente. O `ClientSeeder` cria 10 clientes fictícios de uma agência de viagens.

### Passo 8 — Criar o link de storage

```bash
php artisan storage:link
```

> 💡 Cria um link simbólico entre `public/storage` e `storage/app/public`. Isso é necessário para que avatares e arquivos enviados pelos usuários sejam acessíveis pelo navegador.

### Passo 9 — Iniciar o servidor

```bash
php artisan serve
```

> O servidor vai rodar em **http://127.0.0.1:8000**. Mantenha esse terminal aberto enquanto usa o sistema.

> IMPORTANTE: Ainda é necessário iniciar o FRONTEND (NEXT.JS) usando **npm run dev**. Mantenha esse terminal aberto enquanto utiliza o sistema. -> Mais informações em: desafio-rootcode-frontend\README.md
---

## 📁 Estrutura de pastas

```
desafio-rootcode-api/
│
├── app/
│   └── Http/
│       └── Controllers/
│           ├── AuthController.php          ✅ Login e logout
│           ├── TaskController.php          ✅ CRUD completo de tarefas + lixeira
│           ├── UserController.php          ✅ Listagem e criação de usuários
│           ├── ProfileController.php       ✅ Perfil com métricas + upload de avatar
│           ├── ClientController.php        ✅ CRUD de clientes da agência
│           ├── TaskCommentController.php   ✅ Comentários por tarefa
│           ├── TaskAttachmentController.php✅ Anexos por tarefa
│           ├── Controller.php              📄 Classe base (padrão Laravel — sem lógica)
│           └── Settings/
│               ├── ProfileController.php   📄 Perfil do Fortify (padrão — não usamos)
│               └── SecurityController.php  📄 Segurança do Fortify (padrão — não usamos)
│
│   └── Models/
│       ├── User.php               ✅ Usuário com autenticação Sanctum
│       ├── Task.php               ✅ Tarefa com SoftDelete e relacionamentos
│       ├── Team.php               ✅ Equipes/Times
│       ├── Client.php             ✅ Clientes da agência
│       ├── TaskComment.php        ✅ Comentários de tarefa
│       └── TaskAttachment.php     ✅ Anexos de tarefa
│
│   └── Console/
│       └── Commands/
│           └── LimparLixeira.php  ✅ Comando para deletar tasks expiradas da lixeira
│
├── bootstrap/
│   └── app.php                   ✅ Configura rotas, middlewares e CORS
│
├── database/
│   ├── migrations/               ✅ Estrutura das tabelas do banco (nossa lógica)
│   └── seeders/                  ✅ Dados de teste (nossa lógica)
│       ├── DatabaseSeeder.php     ✅ Orquestra a ordem dos seeders
│       ├── UserSeeder.php         ✅ Cria equipes, gestores e colaboradores
│       └── ClientSeeder.php       ✅ Cria 10 clientes fictícios
│
├── routes/
│   ├── api.php                   ✅ Todas as rotas da API (nossa lógica)
│   ├── console.php               ✅ Agenda o comando lixeira:limpar (a cada 1h)
│   ├── web.php                   📄 Rotas web padrão (não afeta nossa API)
│   └── settings.php              📄 Rotas de configuração do Fortify (padrão)
│
├── storage/
│   └── app/public/
│       ├── avatars/              ✅ Fotos de perfil dos usuários
│       └── attachments/          ✅ Arquivos anexados às tarefas
│
├── vendor/                       📦 Bibliotecas do Composer (NÃO editar)
├── .env                          🔒 Configurações locais (NÃO vai para o GitHub)
├── .env.example                  📄 Modelo do .env para novos desenvolvedores
└── composer.json                 📄 Lista de dependências
```

**Legenda:**
- ✅ Arquivo customizado — contém lógica específica deste projeto
- 📄 Arquivo padrão do Laravel — raramente modificado
- 📦 Gerado automaticamente — nunca editar manualmente
- 🔒 Arquivo sensível — nunca enviar para o GitHub

---

## 🗄️ Banco de dados — tabelas e relações

### Diagrama de relacionamentos

```
┌──────────┐       ┌────────────┐       ┌───────────────────────────┐
│  teams   │──────►│ team_user  │◄──────│           users           │
│──────────│       │────────────│       │───────────────────────────│
│ id       │       │ id         │       │ id                        │
│ name     │       │ team_id    │       │ name                      │
└──────────┘       │ user_id    │       │ email                     │
                   │ role_in_   │       │ password                  │
                   │  team      │       │ role (gestor/colaborador) │
                   └────────────┘       │ avatar                    │
                                        └──────────────┬────────────┘
                                                        │
                               ┌────────────────────────┼──────────────────────────┐
                               │ user_id                │ assigned_to              │
                               │ (criador)              │ reviewer_id  deleted_by  │
                               ▼                        ▼                          │
              ┌──────────┐  ┌──────────────────────────────────────────────────┐   │
              │ clients  │  │                       tasks                      │   │
              │──────────│  │──────────────────────────────────────────────────│   │
              │ id       │  │ id              title            due_date        │   │
              │ name     │──►client_id        description      completed_at    │   │
              │ type     │  │ user_id ────────────────────────────────────────────►│
              │ email    │  │ assigned_to ────────────────────────────────────────►│
              │ phone    │  │ reviewer_id ────────────────────────────────────────►│
              │ document │  │ deleted_by ─────────────────────────────────────────►│
              │ notes    │  │ team_id         status           difficulty          │
              │ segment  │  │ documentation_url               deleted_at           │
              └──────────┘  └─────────────────────┬────────────────────────────────┘
                                                  │
                              ┌───────────────────┼───────────────────┐
                              ▼                   ▼                   ▼
                     ┌──────────────┐   ┌──────────────┐   ┌────────────────────┐
                     │task_comments │   │task_attach-  │   │personal_access_    │
                     │──────────────│   │ments         │   │tokens (Sanctum)    │
                     │ id           │   │──────────────│   │────────────────────│
                     │ task_id      │   │ id           │   │ Tokens de          │
                     │ user_id      │   │ task_id      │   │ autenticação       │
                     │ content      │   │ user_id      │   │ gerados no login   │
                     └──────────────┘   │ filename     │   └────────────────────┘
                                        │ path         │
                                        │ mime_type    │
                                        │ size         │
                                        └──────────────┘
```

### Evolução da tabela `tasks` ao longo das migrations

```
Migration 1: create_tasks_table
─────────────────────────────────
+ id
+ user_id       (quem criou)
+ title
+ description
+ status        (pendente | concluido)
+ difficulty    (facil | medio | dificil)
+ due_date
+ created_at / updated_at

Migration 2: add_assignment_fields_to_tasks_table
─────────────────────────────────────────────────
+ assigned_to       (colaborador responsável)
+ team_id           (time responsável)
+ documentation_url (link para docs externas)
+ reviewer_id       (quem vai revisar)
~ status agora aceita também: em_andamento | em_revisao

Migration 3: add_completed_at_to_tasks_table
─────────────────────────────────────────────
+ completed_at  (registra o momento exato da conclusão)

Migration 4: add_client_id_to_tasks_table
─────────────────────────────────────────
+ client_id     (vincula a task a um cliente da agência)

Migration 5: add_soft_delete_to_tasks_table
────────────────────────────────────────────
+ deleted_at    (soft delete — lixeira de 48hrs)
+ deleted_by    (registra quem deletou a task)
```

### Descrição de cada tabela

| Tabela | Para que serve | Tipo |
|--------|----------------|------|
| `users` | Todos os usuários (gestores e colaboradores) | Customizada |
| `teams` | Equipes/times da agência | Customizada |
| `team_user` | Liga usuários às equipes (muitos-para-muitos) | Customizada |
| `tasks` | As tarefas em si, com todos os campos | Customizada |
| `clients` | Clientes da agência de viagens | Customizada |
| `task_comments` | Comentários feitos em cada tarefa | Customizada |
| `task_attachments` | Arquivos anexados às tarefas | Customizada |
| `personal_access_tokens` | Tokens de autenticação do Sanctum | Padrão Laravel |
| `cache` / `cache_locks` | Cache da aplicação | Padrão Laravel |
| `jobs` / `job_batches` / `failed_jobs` | Filas de processamento | Padrão Laravel |
| `sessions` | Sessões de usuário | Padrão Laravel |
| `password_reset_tokens` | Tokens para redefinição de senha | Padrão Laravel |

---

## 🎮 Controllers — o que cada um faz

### `AuthController.php` ✅
**Responsável por:** Login e logout dos usuários.

```
┌─────────────────────────────────────────────────────────┐
│  login()                                                │
│  ├── Recebe: { email, password }                        │
│  ├── Valida os campos (obrigatórios, formato email)     │
│  ├── Tenta autenticar com Auth::attempt()               │
│  │     ├── Falhou → retorna 401 "Credenciais inválidas" │
│  │     └── OK → busca o usuário autenticado             │
│  ├── Apaga tokens antigos (evita acúmulo no banco)      │
│  ├── Gera novo token via Sanctum                        │
│  └── Retorna: { token, user }                           │
│                                                         │
│  logout()                                               │
│  ├── Recebe: token no header Authorization              │
│  └── Deleta o token atual do banco de dados             │
└─────────────────────────────────────────────────────────┘
```

> 💡 **Por que deletar tokens antigos no login?** Para que cada usuário tenha apenas um token ativo por vez. Se um usuário fizer login em dois dispositivos, o primeiro deixa de funcionar. Isso é uma decisão de segurança — pode ser alterada se necessário.

---

### `TaskController.php` ✅
**Responsável por:** Todas as operações com tarefas (o coração do sistema).

```
┌─────────────────────────────────────────────────────────┐
│  index()                                                │
│  └── Retorna todas as tasks ordenadas por prazo         │
│                                                         │
│  store()  [só gestor]                                   │
│  ├── Verifica se é gestor (403 caso contrário)          │
│  ├── Valida todos os campos                             │
│  └── Cria a task com user_id = usuário logado           │
│                                                         │
│  show()                                                 │
│  └── Retorna uma task específica por ID                 │
│                                                         │
│  update()                                               │
│  ├── Colaborador: verifica se é o assigned_to           │
│  ├── Se status mudou para "concluido":                  │
│  │     └── Preenche completed_at = agora                │
│  ├── Se status saiu de "concluido":                     │
│  │     └── Limpa completed_at = null                    │
│  └── Salva as alterações                                │
│                                                         │
│  destroy()  [só gestor]                                 │
│  ├── Registra deleted_by = id do gestor                 │
│  └── Soft delete → preenche deleted_at                  │
│                                                         │
│  lixeira()  [só gestor]                                 │
│  └── Retorna tasks com deleted_at >= agora - 48hrs      │
│                                                         │
│  restaurar()  [só gestor]                               │
│  ├── Verifica se ainda está dentro das 48hrs            │
│  ├── Restaura a task (limpa deleted_at)                 │
│  └── Limpa deleted_by                                   │
└─────────────────────────────────────────────────────────┘
```

> 💡 **O que é Soft Delete?** Em vez de apagar o registro do banco permanentemente, o Laravel apenas preenche o campo `deleted_at` com a data/hora da exclusão. A task "some" das consultas normais mas ainda existe no banco. O trait `SoftDeletes` no model faz isso automaticamente.

---

### `UserController.php` ✅
**Responsável por:** Listagem e criação de usuários.

```
┌─────────────────────────────────────────────────────────┐
│  gestores()      → Lista só role = 'gestor'             │
│                    (usado no select de "Revisor")       │
│                                                         │
│  colaboradores() → Lista só role = 'colaborador'        │
│                    (usado no select de "Responsável")   │
│                                                         │
│  times()         → Lista todos os times                 │
│                    (usado no select de "Time")          │
│                                                         │
│  todos()         → Lista todos os usuários com avatar   │
│                    Converte o caminho do avatar em URL  │
│                    pública completa via asset()         │
│                    (usado nos cards do Kanban)          │
│                                                         │
│  store()  [só gestor]                                   │
│  ├── Verifica se é gestor                               │
│  ├── Valida: name, email único, password, role          │
│  └── Cria o usuário com senha criptografada (bcrypt)    │
└─────────────────────────────────────────────────────────┘
```

---

### `ProfileController.php` ✅
**Responsável por:** Perfil do colaborador com métricas de desempenho e upload de avatar.

```
┌─────────────────────────────────────────────────────────┐
│  show(User $user)                                       │
│  ├── Busca tasks onde assigned_to = user.id             │
│  ├── Calcula métricas de entregas:                      │
│  │     ├── Esta semana (desde segunda-feira)            │
│  │     ├── Este mês                                     │
│  │     └── Este ano                                     │
│  ├── Calcula tasks ativas por dificuldade               │
│  └── Retorna: { user, metricas, tasks_concluidas,       │
│                 todas_tasks }                           │
│                                                         │
│  uploadAvatar()                                         │
│  ├── Valida: arquivo de imagem, máx 2MB                 │
│  ├── Remove o avatar antigo do storage (se existir)     │
│  ├── Salva em storage/app/public/avatars/               │
│  └── Atualiza user.avatar no banco                      │
└─────────────────────────────────────────────────────────┘
```

---

### `ClientController.php` ✅
**Responsável por:** CRUD completo de clientes da agência.

```
┌─────────────────────────────────────────────────────────┐
│  index()   → Lista clientes em ordem alfabética         │
│                                                         │
│  store()   [só gestor]                                  │
│  └── Valida e cria o cliente                            │
│                                                         │
│  show(Client $client)                                   │
│  ├── Busca todas as tasks do cliente                    │
│  ├── Calcula métricas: total, concluídas,               │
│  │   entregas por semana/mês/ano, por dificuldade       │
│  └── Retorna: { client, metricas, todas_tasks,          │
│                 tasks_concluidas }                      │
│                                                         │
│  update()  [só gestor]  → Edita os dados do cliente     │
│  destroy() [só gestor]  → Deleta o cliente              │
└─────────────────────────────────────────────────────────┘
```

---

### `TaskCommentController.php` ✅
**Responsável por:** Comentários em tarefas.

```
┌─────────────────────────────────────────────────────────┐
│  index()                                                │
│  └── Lista comentários com nome e role do autor         │
│                                                         │
│  store()                                                │
│  ├── Valida: content obrigatório, máx 2000 chars        │
│  └── Cria o comentário com user_id do usuário logado    │
│                                                         │
│  destroy()                                              │
│  ├── Verifica se comment.user_id = usuário logado       │
│  └── Só o autor pode deletar o próprio comentário       │
└─────────────────────────────────────────────────────────┘
```

---

### `TaskAttachmentController.php` ✅
**Responsável por:** Arquivos anexados às tarefas.

```
┌─────────────────────────────────────────────────────────┐
│  index()    → Lista anexos com nome do autor            │
│                                                         │
│  store()                                                │
│  ├── Valida: arquivo obrigatório, máx 10MB              │
│  ├── Salva em storage/app/public/attachments/           │
│  └── Registra: filename, path, mime_type, size          │
│                                                         │
│  destroy()                                              │
│  ├── Verifica se é o autor do upload                    │
│  ├── Remove o arquivo físico do storage                 │
│  └── Remove o registro do banco                         │
│                                                         │
│  download()                                             │
│  └── Retorna o arquivo para download direto             │
└─────────────────────────────────────────────────────────┘
```

---

### `Controller.php` 📄 (padrão Laravel)
Classe base abstrata que todos os controllers estendem. Não tem lógica específica — apenas define o namespace e a estrutura base. Todo controller do Laravel precisa estender essa classe.

---

## 🔵 Models — representação dos dados

Models são classes PHP que representam as tabelas do banco de dados. Cada linha da tabela vira um objeto PHP que você pode manipular como qualquer objeto normal.

### `User.php` ✅

```php
Campos aceitos (fillable via atributo PHP):
  name, email, password, role, avatar

Campos ocultos nas respostas JSON:
  password, two_factor_secret, remember_token

Casts (conversão automática de tipo):
  email_verified_at       → Carbon (objeto de data)
  password                → hashed (aplica bcrypt automaticamente)
  two_factor_confirmed_at → Carbon

Traits:
  HasApiTokens  → Habilita o Sanctum para gerar tokens
  Notifiable    → Permite enviar notificações por email/push
  TwoFactorAuthenticatable → Suporte a 2FA (autenticação em dois fatores)

Relacionamentos:
  teams() → Pertence a vários times (via tabela team_user)
  tasks() → Tem várias tarefas criadas por ele
```

### `Task.php` ✅

```php
Campos aceitos (fillable):
  user_id, assigned_to, client_id, team_id, reviewer_id,
  deleted_by, documentation_url, title, description,
  status, difficulty, due_date, completed_at

Casts (conversão automática):
  due_date     → Carbon date (para cálculos de prazo)
  deleted_at   → Carbon datetime (com horário)
  completed_at → Carbon datetime (com horário)

Trait: SoftDeletes → Habilita a lixeira automática

Relacionamentos:
  user()        → O gestor que criou a task
  assignedTo()  → O colaborador responsável
  client()      → O cliente vinculado à task
  team()        → O time responsável
  reviewer()    → O revisor da task
  deletedBy()   → Quem deletou (para auditoria)
  comments()    → Todos os comentários da task
  attachments() → Todos os arquivos anexados
```

### `Team.php` ✅

```php
Campos aceitos: name

Relacionamentos:
  users()         → Muitos usuários via team_user
                    (inclui campo role_in_team da tabela pivot)
  gestores()      → Atalho: só usuários com role_in_team = 'gestor'
  colaboradores() → Atalho: só usuários com role_in_team = 'colaborador'
```

### `Client.php` ✅

```php
Campos aceitos: name, type, email, phone, document, notes, segment

Relacionamentos:
  tasks() → Tasks vinculadas a esse cliente
```

### `TaskComment.php` ✅

```php
Campos aceitos: task_id, user_id, content

Relacionamentos:
  task() → A tarefa que recebeu o comentário
  user() → O autor do comentário
```

### `TaskAttachment.php` ✅

```php
Campos aceitos: task_id, user_id, filename, path, mime_type, size

Relacionamentos:
  task() → A tarefa que tem o anexo
  user() → Quem fez o upload
```

---

## 🔌 Rotas da API (`routes/api.php`)

### Rotas Públicas

| Método | Endpoint | O que faz |
|--------|----------|-----------|
| `POST` | `/api/login` | Autentica e retorna token |

### Rotas Protegidas — requerem header `Authorization: Bearer {token}`

#### Autenticação
| Método | Endpoint | O que faz |
|--------|----------|-----------|
| `POST` | `/api/logout` | Invalida o token atual |

#### Usuários
| Método | Endpoint | O que faz | Quem pode |
|--------|----------|-----------|-----------|
| `GET` | `/api/usuarios` | Lista todos com avatar | Todos |
| `GET` | `/api/gestores` | Lista só gestores | Todos |
| `GET` | `/api/colaboradores` | Lista só colaboradores | Todos |
| `GET` | `/api/times` | Lista os times | Todos |
| `POST` | `/api/usuarios` | Cria um usuário | Só gestores |

#### Tarefas
| Método | Endpoint | O que faz | Quem pode |
|--------|----------|-----------|-----------|
| `GET` | `/api/tasks` | Lista todas as tasks | Todos |
| `POST` | `/api/tasks` | Cria uma task | Só gestores |
| `GET` | `/api/tasks/lixeira` | Tasks deletadas (48hrs) | Só gestores |
| `POST` | `/api/tasks/{id}/restaurar` | Restaura da lixeira | Só gestores |
| `GET` | `/api/tasks/{task}` | Retorna uma task | Todos |
| `PUT` | `/api/tasks/{task}` | Edita uma task | Gestor: qualquer / Colaborador: só as suas |
| `DELETE` | `/api/tasks/{task}` | Move para lixeira | Só gestores |

#### Comentários
| Método | Endpoint | O que faz |
|--------|----------|-----------|
| `GET` | `/api/tasks/{task}/comments` | Lista comentários |
| `POST` | `/api/tasks/{task}/comments` | Adiciona comentário |
| `DELETE` | `/api/comments/{comment}` | Deleta (só o autor) |

#### Anexos
| Método | Endpoint | O que faz |
|--------|----------|-----------|
| `GET` | `/api/tasks/{task}/attachments` | Lista anexos |
| `POST` | `/api/tasks/{task}/attachments` | Upload de arquivo (máx 10MB) |
| `DELETE` | `/api/attachments/{attachment}` | Deleta (só o autor) |
| `GET` | `/api/attachments/{attachment}/download` | Download do arquivo |

#### Perfil
| Método | Endpoint | O que faz |
|--------|----------|-----------|
| `GET` | `/api/colaboradores/{user}` | Perfil com métricas |
| `POST` | `/api/perfil/avatar` | Upload de foto de perfil |

#### Clientes
| Método | Endpoint | O que faz | Quem pode |
|--------|----------|-----------|-----------|
| `GET` | `/api/clients` | Lista todos | Todos |
| `POST` | `/api/clients` | Cria cliente | Só gestores |
| `GET` | `/api/clients/{client}` | Cliente + métricas + tasks | Todos |
| `PUT` | `/api/clients/{client}` | Edita cliente | Só gestores |
| `DELETE` | `/api/clients/{client}` | Deleta cliente | Só gestores |

---

## 🔒 Regras de negócio

### Matriz de permissões

```
┌─────────────────────────────────┬───────────┬───────────────────────────┐
│ Ação                            │  Gestor   │       Colaborador         │
├─────────────────────────────────┼───────────┼───────────────────────────┤
│ Ver todas as tasks              │    ✅     │           ✅             │
│ Criar tasks                     │    ✅     │           ❌             │
│ Editar qualquer task            │    ✅     │           ❌             │
│ Editar tasks onde é responsável │    ✅     │  ✅ (assigned_to = eu)   │
│ Mover task no Kanban (drag)     │    ✅     │  ✅ (só as próprias)     │
│ Deletar tasks (lixeira)         │    ✅     │           ❌             │
│ Ver e restaurar da lixeira      │    ✅     │           ❌             │
│ Criar usuários                  │    ✅     │           ❌             │
│ Criar clientes                  │    ✅     │           ❌             │
│ Ver clientes e suas tasks       │    ✅     │           ✅             │
│ Comentar em tasks               │    ✅     │           ✅             │
│ Deletar próprio comentário      │    ✅     │           ✅             │
│ Anexar arquivos a tasks         │    ✅     │           ✅             │
│ Deletar próprio anexo           │    ✅     │           ✅             │
│ Ver perfis de colaboradores     │    ✅     │           ✅             │
│ Editar próprio avatar           │    ✅     │           ✅             │
└─────────────────────────────────┴────────────┴──────────────────────────┘
```

### Lixeira de tarefas (48 horas)

```
  Gestor clica em "Excluir"
              │
              ▼
  TaskController::destroy()
  ├── Registra deleted_by = id do gestor
  └── Soft delete → preenche deleted_at = agora
              │
              ▼
  Task some das listagens normais
  (mas ainda existe no banco com deleted_at preenchido)
              │
      ┌───────┴───────────┐
      │                   │
  Dentro de 48hrs     Após 48hrs
      │                   │
      ▼                   ▼
  Gestor pode         Comando lixeira:limpar
  restaurar           (roda a cada 1 hora via schedule)
                      apaga permanentemente com forceDelete()
```

### completed_at automático

```
  Usuário move task para "Concluído"
              │
              ▼
  TaskController::update()
  ├── Detecta que status mudou para 'concluido'
  └── Preenche completed_at = now() (data e hora exatos)

  Usuário move task de "Concluído" para outro status
              │
              ▼
  TaskController::update()
  ├── Detecta que status saiu de 'concluido'
  └── Limpa completed_at = null
```

---

## 👤 Dados de teste

Após rodar `php artisan db:seed`, os seguintes dados estarão disponíveis:

### Usuários

O `UserSeeder` cria **3 equipes**, **10 gestores** e **10 colaboradores** usando o Factory do Laravel (dados aleatórios). A senha de todos é:

```
Senha padrão: password
```
Para pegar um email de gestor pelo terminal:

```bash
php artisan tinker
>>> App\Models\User::where('role', 'gestor')->first()->only(['name', 'email'])
```

### Usuários de teste padrão

Além disso, também inclui usuários fixos de gestores e colaboradores:

```
Email:  gestor@rootcode.com
Senha:  password

Email:  colaborador@rootcode.com  
Senha:  password
```

### Clientes (criados pelo `ClientSeeder`)

| Nome | Tipo | Segmento |
|------|------|----------|
| Petrobras S.A. | Empresa | Corporativo |
| Embraer S.A. | Empresa | Corporativo |
| Ricardo e Fernanda Oliveira | Pessoa Física | Lua de Mel |
| Clube de Aventureiros SP | Empresa | Aventura |
| MSC Cruzeiros Brasil | Empresa | Cruzeiros |
| Família Souza | Pessoa Física | Lazer |
| Grupo Escolar Colégio Elite | Empresa | Grupos |
| Vale S.A. | Empresa | Corporativo |
| Ana Carolina Mendes | Pessoa Física | Aventura |
| Banco Itaú S.A. | Empresa | Corporativo |

---

## 🛠️ Comandos úteis

```bash
# ─── SERVIDOR ────────────────────────────────────────────
php artisan serve
# Inicia em http://127.0.0.1:8000

# ─── BANCO DE DADOS ──────────────────────────────────────
php artisan migrate
# Cria as tabelas (roda migrations pendentes)

php artisan migrate:fresh
# ⚠️ Apaga TUDO e recria do zero (perde dados!)

php artisan migrate:fresh --seed
# ⚠️ Apaga tudo, recria e popula com dados de teste

php artisan db:seed
# Popula sem recriar tabelas

php artisan db:seed --class=ClientSeeder
# Roda apenas o ClientSeeder

# ─── STORAGE ─────────────────────────────────────────────
php artisan storage:link
# Cria o link público para avatares e anexos

# ─── LIXEIRA ─────────────────────────────────────────────
php artisan lixeira:limpar
# Roda manualmente o comando de limpeza
# (normalmente roda automático a cada 1 hora)

# ─── DEBUG ───────────────────────────────────────────────
php artisan tinker
# Console interativo para testar consultas PHP/Eloquent

php artisan route:list
# Lista todas as rotas registradas

# ─── CACHE ───────────────────────────────────────────────
php artisan cache:clear
php artisan config:clear
php artisan route:clear
# Limpa os caches (use quando algo parecer desatualizado)
```

---

## 📁 Arquivos padrão do Laravel (não customizados)

Estes arquivos existem em **todo** projeto Laravel e não contêm lógica específica deste sistema. Você não precisa modificá-los para entender ou rodar o projeto:

| Arquivo / Pasta | Para que serve |
|-----------------|----------------|
| `vendor/` | Bibliotecas instaladas pelo Composer. **Nunca edite.** |
| `config/` | Configurações globais (cache, mail, database, etc.) |
| `public/index.php` | Ponto de entrada da aplicação — recebe todas as requisições HTTP |
| `artisan` | CLI do Laravel — permite rodar `php artisan ...` |
| `composer.json` | Lista de dependências do projeto |
| `composer.lock` | Versões exatas instaladas (garante igualdade entre máquinas) |
| `bootstrap/providers.php` | Registro automático de providers (padrão Laravel 11+) |
| `create_cache_table.php` | Migration da tabela de cache (padrão Laravel) |
| `create_jobs_table.php` | Migration das tabelas de fila (padrão Laravel) |
| `add_two_factor_columns_to_users_table.php` | Colunas do Fortify 2FA (padrão) |
| `create_personal_access_tokens_table.php` | Tabela do Sanctum (padrão) |
| `routes/web.php` | Rotas web com sessão — não usamos para a API |
| `routes/settings.php` | Rotas de configuração do Fortify — não usamos |
| `Settings/ProfileController.php` | Controller de perfil do Fortify — não usamos |
| `Settings/SecurityController.php` | Controller de segurança do Fortify — não usamos |

> 💡 **Por que esses arquivos existem se não usamos?** Este projeto foi criado a partir de um starter kit Laravel que inclui o Fortify (autenticação avançada) e Inertia (frontend integrado). Nós optamos por usar nossa própria autenticação via Sanctum + API separada, então esses arquivos ficaram sem uso mas não prejudicam o funcionamento.