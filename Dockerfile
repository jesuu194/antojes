FROM php:8.2-cli

WORKDIR /app

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev \
    && docker-php-ext-install zip

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copiar archivos
COPY . .

# Instalar dependencias de Symfony
RUN composer install --no-dev --optimize-autoloader

# Limpiar y calentar cache en prod
RUN php bin/console cache:clear --env=prod || true
RUN php bin/console cache:warmup --env=prod || true

# Arrancar el servidor en el puerto que Render da
CMD php -S 0.0.0.0:$PORT -t public