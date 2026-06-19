FROM php:8.4-fpm

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip libpq-dev nodejs npm

# Instalar extensões PHP
RUN docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd
RUN pecl install pcov && echo "extension=pcov.so" > /usr/local/etc/php/php.ini

# Evitar erro de permissão do Git
RUN git config --global --add safe.directory /var/www

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

#   Ajustes específicos para Produção

# Copiar todo o código do projeto para dentro do container
COPY . .

# Copiar o arquivo de ambiente de produção (caso exista) ou o .example temporariamente
RUN cp .env.example .env

# Instalar dependências do Composer otimizadas para produção (--no-dev)
RUN composer install --no-dev --optimize-autoloader

# Instalar dependências do Node e buildar os assets (Vite)
RUN npm install
RUN npm run build

# Ajustar as permissões de escrita do Laravel nas pastas de cache e logs
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
RUN chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Comando de inicialização: Roda as migrations e inicia o servidor na porta exigida ($PORT)
ENTRYPOINT ["sh", "-c", "php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT"]