<img width="4000" height="800" alt="Image" src="https://github.com/user-attachments/assets/5c23eff5-a6d5-400d-8a69-05a91d13c86f" />

# Descrição do Projeto

O TatuDoEstudado é um sistema de organização de estudos desenvolvido com o objetivo de ajudar estudantes a planejarem, acompanharem e otimizarem sua rotina de aprendizado. A plataforma oferece um cronograma automatizado baseado na disponibilidade do usuário, promovendo maior eficiência e constância nos estudos.

# Problemática

A falta de planejamento estruturado impacta diretamente no rendimento acadêmico e muitos estudantes enfrentam dificuldades para:

- Organizar uma rotina de estudos consistente
- Conciliar diferentes disciplinas e prazos
- Revisar conteúdos de forma eficiente
- Evitar procrastinação e distrações
- Acompanhar seu próprio desempenho

# Solução Proposta

O sistema propõe:

- Geração automática de um cronograma personalizado
- Organização inteligente de estudos (teoria, revisão e exercícios)
- Priorização de conteúdos com maior dificuldade
- Reorganização dinâmica da rotina
- Registro e acompanhamento de erros (caderno de erros)

# Tecnologias Utilizadas

O sistema segue o padrão MVC (Model-View-Controller) utilizando:

- Backend: PHP com Laravel
- Frontend: Blade + Tailwind CSS
- Banco de dados: PostgreSQL
- Versionamento: Git e GitHub
- Containerização: Docker
- Prototipação: Figma

# Como rodar o projeto (desenvolvimento)

## Requisitos

- PHP **8.2+** e Composer
- Node.js **18+** e npm
- Docker + Docker Compose (recomendado para o PostgreSQL/pgAdmin)

## 1) Subir o banco (PostgreSQL + pgAdmin)

Na raiz do projeto:

```bash
docker compose up -d
```

Serviços:

- PostgreSQL em `localhost:5432`
- pgAdmin em `http://localhost:5050`
  - e-mail: `tatudoestudado@gmail.com`
  - senha: `admin`

No pgAdmin, ao criar a conexão com o servidor PostgreSQL (dentro do Docker):

- **Host name/address**: `postgres`
- **Port**: `5432`
- **Username**: `tatudoestudado_user`
- **Password**: `tatudoestudado_password`

## 2) Configurar `.env`, instalar as dependências e gerar a chave

```bash
cp .env.example .env
```

Edite o `.env` para usar PostgreSQL (valores compatíveis com o `docker-compose.yml`):

```dotenv
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=tatudoestudado_db
DB_USERNAME=tatudoestudado_user
DB_PASSWORD=tatudoestudado_password
```

Instalar dependências:
```bash
composer install
npm install
```

Gere o `APP_KEY`:

```bash
php artisan key:generate
```


## 3) criar as tabelas

```bash
php artisan migrate
```

## 4) Rodar a aplicação

Opção A (recomendada): tudo junto (server + queue + vite):

```bash
composer run dev
```

Ou manualmente:

```bash
# terminal 1
php artisan serve

# terminal 2
npm run dev
```

A aplicação fica disponível em `http://localhost:8000`.

## Comandos úteis

- Setup automatizado (assumindo `.env` pronto e banco acessível): `composer run setup`
- Resetar o banco (apaga todas as tabelas e recria): `php artisan migrate:fresh`
- Resetar o banco e popular dados (seed): `php artisan migrate:fresh --seed`
- Reaplicar migrations (rollback + migrate): `php artisan migrate:refresh`
- Rodar testes: `composer test`
- Build do front (produção): `npm run build`

# Licença

Este projeto está licenciado sob a Licença MIT.
