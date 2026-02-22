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

# Instalar dependencias
RUN composer install --no-dev --optimize-autoloader

# Permisos
RUN mkdir -p var/cache var/log && chmod -R 777 var

# Mostrar errores PHP
ENV PHP_DISPLAY_ERRORS=1
ENV PHP_DISPLAY_STARTUP_ERRORS=1

# Arranque simple, sin doctrine por ahora (para ver el error real)
CMD php -d display_errors=1 -d display_startup_errors=1 -S 0.0.0.0:$PORT -t public