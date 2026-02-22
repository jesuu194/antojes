FROM php:8.2-cli

WORKDIR /app

# Instalar composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copiar código
COPY . .

# Instalar dependencias
RUN composer install --no-dev --optimize-autoloader

# Calentar cache
RUN php bin/console cache:warmup --env=prod

# Exposar puerto (Render lo asignará)
EXPOSE 8000

# Comando para iniciar
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
