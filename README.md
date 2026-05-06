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

# Como rodar o projeto (Recomendado)

- Para primeira execução, utilize `make setup`.
- Para execuções seguintes, utilize `make up` + `make dev`.

## Requisitos

- Docker
- Docker Compose

## 1) Setup inicial (primeira execução)

Na raiz do projeto:
```bash
make setup
```

> [!TIP]
> Caso o comando `make setup` falhe, você pode configurar o ambiente manualmente criando (copiando) o arquivo `.env` a partir do exemplo.
> Para alterar a configuração do banco de dados, edite (no .env)
> ```dotenv
> DB_CONNECTION=pgsql
> DB_HOST=postgres
> DB_PORT=5432
> DB_DATABASE=tatudoestudado_db
> DB_USERNAME=tatudoestudado_user
> DB_PASSWORD=tatudoestudado_password
> ```

## 2) Rodar a aplicação

```bash
make dev
```

## 3) Serviços da aplicação

- Aplicação: `http://localhost:8000`
- Vite: `http://localhost:5173`
- pgAdmin em `http://localhost:5050`
  - e-mail: `tatudoestudado@gmail.com`
  - senha: `admin`

No pgAdmin, ao criar a conexão com o servidor PostgreSQL (dentro do Docker):

- **Host name/address**: `postgres`
- **Port**: `5432`
- **Username**: `tatudoestudado_user`
- **Password**: `tatudoestudado_password`


## Comandos úteis

### Ambiente

| Comando        | Descrição                                 |
| -------------- | ----------------------------------------- |
| `make up`      | Sobe os containers                        |
| `make stop`    | Para os containers sem remover            |
| `make down`    | Remove os containers                      |
| `make destroy` | Remove containers e volumes (reset total) |
| `make build`   | Sobe os containers com rebuild            |
| `make shell`   | Entra no terminal (bash) do container     |

---

### Setup

| Comando        | Descrição                                     |
| -------------- | --------------------------------------------- |
| `make setup`   | Setup completo (build, install, key, migrate) |
| `make install` | Instala dependências (Composer + npm)         |

---

### Desenvolvimento

| Comando    | Descrição                          |
| ---------- | ---------------------------------- |
| `make dev` | Inicia Laravel + Vite (hot reload) |

---

### Banco de dados

| Comando        | Descrição                   |
| -------------- | --------------------------- |
| `make migrate` | Executa migrations          |
| `make fresh`   | Recria banco e roda seeders |

---

### Artisan & Composer

| Comando                   | Descrição                         |
| ------------------------  | --------------------------------  |
| `make artisan cmd="..."`  | Executa qualquer comando artisan  |
| `make composer cmd="..."` | Executa qualquer comando composer |

Exemplos:

```bash
# Artisan
make artisan cmd="migrate:status"
make artisan cmd="make:model User -m"

# Composer
make composer cmd="require laravel/telescope"
make composer cmd="update"
```

---

### Qualidade de código

| Comando     | Descrição              |
| ----------- | ---------------------- |
| `make lint` | Executa o Laravel Pint |



## Observações
- O projeto roda completamente dentro do Docker
- Não é necessário instalar PHP, Composer ou Node.js na máquina
- Dependências são instaladas dentro do container
- O diretório do projeto é montado como volume para permitir hot reload

# Qualidade de Código e Commits

Utilizamos **Husky** e **Commitlint** para garantir que as mensagens sigam o padrão [Conventional Commits](https://www.conventionalcommits.org/pt-br/v1.0.0/).
- **Padrão:** `tipo: descrição` (Ex: `feat: login`, `fix: erro banco`).
- **Linter:** O **Laravel Pint** formata o código automaticamente no pré-commit.

> **Dica:** Se o commit falhar no VS Code (Linux), rode: 
> `mkdir -p ~/.config/husky && echo 'export PATH="$PATH:$(dirname $(which node)):$(dirname $(which npx))"' > ~/.config/husky/init.sh`

# Licença

Este projeto está licenciado sob a Licença MIT.

# Contribuidores

Agradecemos às seguintes pessoas que contribuíram para o projeto **TaTudoEstudado**:

<table justify-content="center">
  <tr>
    <td align="center">
      <a href="https://github.com/alefCauan">
        <img src="https://avatars.githubusercontent.com/u/149737667?v=4" width="115px;" alt="Alef Cauan"/><br>
        <sub><b>Alef Cauan</b></sub>
      </a><br>
      <a href="mailto:alef.rodrigues@ufpi.edu.br"><img src="https://img.shields.io/badge/-Email-D14836?style=flat-square&logo=gmail&logoColor=white" /></a>
    </td>
    <td align="center">
      <a href="https://github.com/cristinaadms">
        <img src="https://avatars.githubusercontent.com/u/145992979?v=4" width="115px;" alt="Cristina de Moura"/><br>
        <sub><b>Cristina de Moura</b></sub>
      </a><br>
      <a href="mailto:cristina.sousa@ufpi.edu.br"><img src="https://img.shields.io/badge/-Email-D14836?style=flat-square&logo=gmail&logoColor=white" /></a>
    </td>
    <td align="center">
      <a href="https://github.com/gabreudev">
        <img src="https://avatars.githubusercontent.com/u/110724864?v=4" width="115px;" alt="Gabriel Alves"/><br>
        <sub><b>Gabriel Alves</b></sub>
      </a><br>
      <a href="mailto:gabriel.freitas.gf@ufpi.edu.br"><img src="https://img.shields.io/badge/-Email-D14836?style=flat-square&logo=gmail&logoColor=white" /></a>
    </td>
  </tr>
  <tr>
    <td align="center">
      <a href="https://github.com/MarcioRobt0">
        <img src="https://avatars.githubusercontent.com/u/157633101?v=4" width="115px;" alt="Márcio Roberto"/><br>
        <sub><b>Márcio Roberto</b></sub>
      </a><br>
      <a href="mailto:marcio.rodrigues@ufpi.edu.br"><img src="https://img.shields.io/badge/-Email-D14836?style=flat-square&logo=gmail&logoColor=white" /></a>
    </td>
    <td align="center">
      <a href="https://github.com/Malujoro">
        <img src="https://avatars.githubusercontent.com/u/45736178?v=4" width="115px;" alt="Mateus da Rocha"/><br>
        <sub><b>Mateus da Rocha</b></sub>
      </a><br>
      <a href="mailto:mateus.sousa@ufpi.edu.br"><img src="https://img.shields.io/badge/-Email-D14836?style=flat-square&logo=gmail&logoColor=white" /></a>
    </td>
    <td align="center">
      <a href="https://github.com/Malujoro/TatudoEstudado">
          <img width="115px" alt="Image" src="https://github.com/user-attachments/assets/b7c644c5-2f5d-4cc3-89c0-32060041a1b2" /><br>
          <sub><b>TaTudoEstudado</b></sub>
        </a><br>
        <img src="https://img.shields.io/badge/Status-Ativo-brightgreen?style=flat-square" />
    </td>
  </tr>
</table>