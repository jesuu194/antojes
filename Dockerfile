FROM php:8.2-cli

WORKDIR /app

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev \
    && docker-php-ext-install zip pdo pdo_sqlite

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copiar proyecto
COPY . .

# Instalar dependencias de Symfony
RUN composer install --no-dev --optimize-autoloader

# Limpiar cache (no falla si no existe)
RUN php bin/console cache:clear --env=prod || true

# Comando de arranque:
# 1. Crear DB si no existe
# 2. Ejecutar migraciones (o schema:update si no usas migraciones)
# 3. Arrancar servidor PHP
CMD sh -c "php bin/console doctrine:database:create --if-not-exists || true && \
           php bin/console doctrine:migrations:migrate --no-interaction || php bin/console doctrine:schema:update --force || true && \
           php -S 0.0.0.0:$PORT -t public"