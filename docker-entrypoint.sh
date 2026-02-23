#!/bin/sh
# NO usar set -e para que el servidor arranque aunque fallen migraciones

echo "🚀 Starting deployment..."
echo "Environment: APP_ENV=${APP_ENV:-not-set}"
echo "Port: ${PORT:-10000}"

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
    
    # Verificar si necesitamos crear el schema
    echo "📋 Checking database schema..."
    if php bin/console doctrine:schema:validate 2>&1 | grep -q "NOT in sync"; then
        echo "📦 Creating database schema..."
        if php bin/console doctrine:schema:create --no-interaction 2>&1; then
            echo "✅ Schema created"
            
            # Cargar fixtures solo en el primer despliegue
            echo "👥 Loading initial data (fixtures)..."
            if php bin/console doctrine:fixtures:load --no-interaction 2>&1; then
                echo "✅ Fixtures loaded successfully"
            else
                echo "⚠️ Fixtures failed - continuing anyway"
            fi
        else
            echo "⚠️ Schema creation failed, trying migrations..."
            # Si falla la creación, intentar migraciones
            if php bin/console doctrine:migrations:migrate --no-interaction 2>&1; then
                echo "✅ Migrations completed"
            else
                echo "⚠️ Migrations also failed"
            fi
        fi
    else
        echo "✅ Schema is valid"
        # Ejecutar migraciones si existen
        echo "📦 Running migrations (if any)..."
        php bin/console doctrine:migrations:migrate --no-interaction 2>&1 || echo "⚠️ No migrations to run"
    fi
else
    echo "⚠️ Starting server without database (this will likely fail)"
fi

echo "✅ Starting PHP server..."
echo "📂 Public directory contents:"
ls -la /app/public/*.html 2>/dev/null || echo "No HTML files found"

# Iniciar servidor
exec php -S 0.0.0.0:${PORT:-10000} -t /app/public /app/public/router.php
