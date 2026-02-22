FROM php:8.2-cli

WORKDIR /app

# Instalar dependencias del sistema y extensiones PHP necesarias
RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev sqlite3 \
    && docker-php-ext-install zip pdo pdo_sqlite

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copiar proyecto
COPY . .

# Instalar dependencias de Symfony
RUN composer install --no-dev --optimize-autoloader

# Asegurar permisos en carpetas de cache/log
RUN mkdir -p var/cache var/log && chmod -R 777 var

# Limpiar cache (no falla si algo va mal)
RUN php bin/console cache:clear --env=prod || true

# Arranque:
# - Crear DB si no existe
# - Crear esquema (si no hay migraciones)
# - Arrancar servidor PHP
CMD sh -c "php bin/console doctrine:database:create --if-not-exists || true && \
           php bin/console doctrine:schema:update --force || true && \
           php -S 0.0.0.0:$PORT -t public"