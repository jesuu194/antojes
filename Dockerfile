FROM php:8.2-cli

WORKDIR /app

RUN apt-get update && apt-get install -y sqlite3 \
    && docker-php-ext-install pdo pdo_sqlite

COPY . .

RUN mkdir -p var/cache var/log && chmod -R 777 var

# Arranque:
# - Crear esquema de la DB automáticamente
# - Arrancar servidor
CMD sh -c "php bin/console doctrine:schema:update --force || true && php -S 0.0.0.0:$PORT -t public"