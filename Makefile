DOCKER_COMPOSE = docker compose
APP_CONTAINER = app

.PHONY: up build stop down destroy install setup dev migrate fresh lint

# Sobe os containers e inicia o servidor
up:
	$(DOCKER_COMPOSE) up -d

# Builda a aplicação
build:
	$(DOCKER_COMPOSE) up -d --build

# Para tudo (mantém estado)
stop:
	$(DOCKER_COMPOSE) stop

# Remove containers
down:
	$(DOCKER_COMPOSE) down

# Remove containers e volume
destroy:
	$(DOCKER_COMPOSE) down -v

# Instala dependências dentro do container
install:
	$(DOCKER_COMPOSE) exec $(APP_CONTAINER) composer install
	$(DOCKER_COMPOSE) exec $(APP_CONTAINER) npm install

# Espera até que o APP_CONTAINER fique disponível
wait:
	@echo "Aguardando container ficar disponível..."
	@until docker compose exec $(APP_CONTAINER) php -v > /dev/null 2>&1; do \
		sleep 1; \
	done

# Setup inicial dentro do Docker
setup: build
	cp --update=none .env.example .env || true
	$(MAKE) wait
	${MAKE} install
	$(DOCKER_COMPOSE) exec $(APP_CONTAINER) php artisan key:generate
	$(DOCKER_COMPOSE) exec $(APP_CONTAINER) php artisan migrate
	@echo "Projeto pronto para uso!"

# Comando para rodar Laravel + Vite dentro do Docker
dev:
	$(DOCKER_COMPOSE) exec $(APP_CONTAINER) npm run dev -- --host &
	$(DOCKER_COMPOSE) exec $(APP_CONTAINER) php artisan serve --host=0.0.0.0 --port=8000

# Atalho para artisan genérico
artisan:
	$(DOCKER_COMPOSE) exec $(APP_CONTAINER) php artisan $(cmd)

# Atalhos para comandos artisan
migrate:
	$(DOCKER_COMPOSE) exec $(APP_CONTAINER) php artisan migrate

fresh:
	$(DOCKER_COMPOSE) exec $(APP_CONTAINER) php artisan migrate:fresh --seed

lint:
	$(DOCKER_COMPOSE) exec $(APP_CONTAINER) ./vendor/bin/pint