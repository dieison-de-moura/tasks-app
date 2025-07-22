# Tasks App

Sistema de gerenciamento de tarefas desenvolvido com Laravel, Vue 3, Inertia.js e Docker. Permite que usuários autenticados criem, visualizem, editem e excluam tarefas, além de acompanhar o status e prazos de cada uma.

## Tecnologias Utilizadas

- **Backend:** PHP 8.2+, Laravel 12, Inertia.js, MySQL
- **Frontend:** Vue 3, Vite, TailwindCSS
- **Ambiente:** Docker, Docker Compose, Nginx, PHPMyAdmin

## Funcionalidades

- Cadastro e autenticação de usuários
- CRUD de tarefas (criar, listar, editar, excluir)
- Filtros por status e ordenação
- Interface responsiva e moderna
- Gerenciamento de prazos e status das tarefas

## Variáveis de Ambiente

Copie o arquivo `.env.example` para `.env` e ajuste conforme necessário:

```sh
cp .env.example .env
```

Principais variáveis:
- `APP_NAME`, `APP_ENV`, `APP_KEY`, `APP_DEBUG`, `APP_URL`
- `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `SESSION_DRIVER`, `QUEUE_CONNECTION`, `CACHE_STORE`

> **Obs:** O projeto já está configurado para rodar com MySQL via Docker.

## Como executar localmente

### Pré-requisitos
- [Docker](https://www.docker.com/get-started/) e [Docker Compose](https://docs.docker.com/compose/install/)
- Make (opcional, para facilitar comandos)

### 1. Suba os containers

```sh
docker compose up -d --build
```

Isso irá subir os serviços:
- **app**: PHP/Laravel
- **nginx**: Servidor web
- **db**: MySQL
- **phpmyadmin**: Interface para o banco de dados

Aguarde até que todos os containers estejam prontos.

### 2. Instale as dependências

No container da aplicação:

```sh
docker compose exec app composer install
```

No frontend:

```sh
docker compose exec app npm install
```

### 3. Gere a chave da aplicação

```sh
docker compose exec app php artisan key:generate
```

### 4. Execute as migrations

```sh
docker compose exec app php artisan migrate
```

### 5. Rode o servidor de desenvolvimento

O Vite será iniciado junto com o backend. Acesse:

- **Frontend:** http://localhost:8001
- **PHPMyAdmin:** http://localhost:8002 (usuário: root, senha: root)

## Scripts Úteis

- `composer test` — executa os testes automatizados
- `npm run dev` — inicia o frontend em modo desenvolvimento
- `php artisan` — comandos do Laravel

## Estrutura dos Containers

- `app`: Código-fonte Laravel + Node
- `nginx`: Servidor web
- `db`: Banco de dados MySQL
- `phpmyadmin`: Interface de administração do banco

## Licença

MIT. Veja o arquivo LICENSE para mais detalhes. 