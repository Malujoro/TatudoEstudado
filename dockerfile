# Altere de 8.2 para 8.4
FROM php:8.4-fpm

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip libpq-dev nodejs npm

# Instalar extensões PHP
RUN docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd

# Resolve o erro: fatal: detected dubious ownership in repository at '/var/www'
RUN git config --global --add safe.directory /var/www

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www