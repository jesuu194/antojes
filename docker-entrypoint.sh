#!/bin/sh
# NO usar set -e para que el servidor arranque aunque fallen migraciones

echo "🚀 Starting deployment..."
echo "Environment: APP_ENV=${APP_ENV:-not-set}"

# Railway inyecta PORT automáticamente, pero por si acaso:
if [ -z "$PORT" ]; then
    export PORT=10000
fi
echo "Port: $PORT"

# Función para esperar a que la base de datos esté disponible
wait_for_db() {
    echo "⏳ Waiting for database to be ready..."
    max_attempts=30
    attempt=1
    
    while [ $attempt -le $max_attempts ]; do
        echo "  Attempt $attempt/$max_attempts..."
        
        if php bin/console dbal:run-sql "SELECT 1" >/dev/null 2>&1; then
            echo "✅ Database is ready!"
            return 0
        fi
        
        echo "  Database not ready yet, waiting 2 seconds..."
        sleep 2
        attempt=$((attempt + 1))
    done
    
    echo "❌ Database connection timeout after $max_attempts attempts"
    return 1
}

# Esperar a que PostgreSQL esté disponible
if wait_for_db; then
    echo "🔍 Database connection established"
    
    # Crear el schema directamente (sin migraciones para evitar problemas SQLite/PostgreSQL)
    echo "📦 Creating database schema..."
    if php bin/console doctrine:schema:create --no-interaction 2>&1 | grep -q "already exists"; then
        echo "✅ Schema already exists"
    elif php bin/console doctrine:schema:create --no-interaction 2>&1; then
        echo "✅ Schema created successfully"
        
        # Cargar fixtures solo en el primer despliegue
        echo "👥 Loading initial data (fixtures)..."
        if php bin/console doctrine:fixtures:load --no-interaction 2>&1; then
            echo "✅ Fixtures loaded successfully"
        else
            echo "⚠️ Fixtures failed - continuing anyway"
        fi
    else
        echo "⚠️ Schema creation failed, database might already be initialized"
    fi
else
    echo "⚠️ Starting server without database (this will likely fail)"
fi

echo "✅ Starting PHP server..."
echo "📂 Public directory contents:"
ls -la /app/public/*.html 2>/dev/null || echo "No HTML files found"

# Iniciar servidor en el puerto correcto
echo "Starting server on 0.0.0.0:$PORT"
exec php -S "0.0.0.0:$PORT" -t /app/public /app/public/router.php
