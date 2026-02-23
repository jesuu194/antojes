# 🔧 Solución de Problemas en Render

## ❌ Error: "Failed to fetch" con PostgreSQL

Este es el error más común al desplegar en Render. Aquí están las soluciones:

### ✅ Solución 1: Verificar la DATABASE_URL

**El error ocurre cuando:**
- La URL de la base de datos no está configurada correctamente
- Usas la URL "Externa" en lugar de la "Interna"

**Solución:**
1. Ve a tu PostgreSQL database en Render
2. En "Info" busca **"Internal Database URL"** (NO la External)
3. Copia esa URL (empieza con `postgresql://`)
4. En tu Web Service → Environment → DATABASE_URL
5. Pega la URL **INTERNA**

**Ejemplo correcto:**
```
postgresql://user:password@dpg-xxxxx-internal/database
```

**Ejemplo incorrecto (External):**
```
postgresql://user:password@dpg-xxxxx.oregon-postgres.render.com/database
```

### ✅ Solución 2: Misma Región para DB y Web Service

**El error ocurre cuando:**
- La base de datos y el web service están en regiones diferentes
- Render no puede conectar entre regiones diferentes

**Solución:**
1. Verifica la región de tu PostgreSQL (ej: Frankfurt)
2. Verifica la región de tu Web Service
3. **Deben estar en la MISMA región**
4. Si no coinciden, elimina uno y créalo de nuevo en la región correcta

### ✅ Solución 3: Esperar a que PostgreSQL esté Listo

Nuestro `docker-entrypoint.sh` ya tiene lógica de reintentos que:
- Espera hasta 60 segundos (30 intentos × 2 segundos)
- Verifica la conexión antes de ejecutar migraciones
- Muestra el progreso en los logs

**Para verificar en los logs:**
```
⏳ Waiting for database to be ready...
  Attempt 1/30...
  Attempt 2/30...
✅ Database is ready!
```

Si ves muchos intentos (más de 10), revisa la DATABASE_URL.

### ✅ Solución 4: Recrear la Base de Datos

Si todo lo anterior falla:

1. **Suspender el Web Service:**
   - Web Service → Settings → "Suspend Web Service"

2. **Eliminar la base de datos antigua:**
   - Ve a tu PostgreSQL database
   - Settings → "Delete Database"
   - Confirma la eliminación

3. **Crear nueva base de datos:**
   - New + → PostgreSQL
   - **Name:** `antojes-db`
   - **Region:** Frankfurt (EU Central) - **LA MISMA que tu Web Service**
   - **Plan:** Free
   - Create Database

4. **Copiar Internal Database URL:**
   - Espera a que esté "Available"
   - Copia la **Internal Database URL**

5. **Actualizar Web Service:**
   - Ve a tu Web Service
   - Environment → DATABASE_URL → Edit
   - Pega la nueva Internal URL
   - Save Changes

6. **Resumir Web Service:**
   - Settings → "Resume Web Service"
   - O hacer un manual deploy

### ✅ Solución 5: Usar Blueprint (Más Fácil)

En lugar de crear todo manualmente, usa el `render.yaml`:

1. Ve a https://dashboard.render.com/
2. Click **"New +"** → **"Blueprint"**
3. Conecta el repo: `jesuu194/antojes`
4. Render crea TODO automáticamente:
   - ✅ PostgreSQL en la región correcta
   - ✅ Web Service vinculado
   - ✅ DATABASE_URL configurada automáticamente
   - ✅ Todas las variables de entorno

## 🐛 Otros Problemas Comunes

### Error: "Schema not in sync"

**Solución desde el Shell de Render:**
```bash
php bin/console doctrine:schema:drop --force
php bin/console doctrine:schema:create
php bin/console doctrine:fixtures:load --no-interaction
```

### Error: "Port 10000 already in use"

Esto es normal, Render lo maneja. No hagas nada.

### Error: "Composer install failed"

Verifica que `composer.json` y `composer.lock` estén en el repo:
```bash
git add composer.json composer.lock
git commit -m "Add composer files"
git push origin main
```

### El servicio se queda "In Progress" mucho tiempo

1. Ve a **Logs**
2. Busca errores en rojo
3. Si ves "Waiting for database", es normal hasta 60 segundos
4. Si pasa de 60 segundos, hay un problema con DATABASE_URL

### Base de datos vacía después del deploy

Las fixtures solo se cargan en el primer deploy. Para recargarlas:

1. Shell de Render:
```bash
php bin/console doctrine:fixtures:load --no-interaction --append
```

2. O si quieres resetear todo:
```bash
php bin/console doctrine:schema:drop --force
php bin/console doctrine:schema:create
php bin/console doctrine:fixtures:load --no-interaction
```

### Error: "Class not found" o "Autoload error"

```bash
# Desde el Shell de Render:
composer dump-autoload --optimize
```

### Logs no muestran nada útil

Agrega más logging temporal en `docker-entrypoint.sh`:
```bash
echo "DEBUG: DATABASE_URL = ${DATABASE_URL}"
php bin/console dbal:run-sql "SELECT current_database(), version()"
```

## 📊 Comandos Útiles en Shell de Render

### Ver usuarios en la base de datos:
```bash
php bin/console doctrine:query:sql "SELECT id, name, email FROM \"user\" LIMIT 10"
```

### Ver estado de la base de datos:
```bash
php bin/console doctrine:schema:validate
```

### Ver migraciones pendientes:
```bash
php bin/console doctrine:migrations:status
```

### Limpiar cache:
```bash
php bin/console cache:clear
```

### Test de conexión a PostgreSQL:
```bash
php bin/console dbal:run-sql "SELECT version()"
```

## 🎯 Checklist para Deploy Exitoso

- [ ] PostgreSQL creada en la región correcta
- [ ] Web Service en la MISMA región
- [ ] DATABASE_URL es la **Internal** URL
- [ ] DATABASE_URL comienza con `postgresql://`
- [ ] Todas las variables de entorno configuradas
- [ ] Los logs muestran "✅ Database is ready!"
- [ ] Los logs muestran "✅ Schema created" o "✅ Migrations completed"
- [ ] El servicio está "Live"
- [ ] `/api/health` responde correctamente

## 🆘 Si Nada Funciona

1. **Elimina todo:**
   - Suspende el Web Service
   - Elimina PostgreSQL
   - Elimina Web Service

2. **Usa Blueprint (automático):**
   ```
   Dashboard → New + → Blueprint → Conecta repo
   ```

3. **O contacta con soporte de Render:**
   - help@render.com
   - Discord: https://discord.gg/render

## ✅ Configuración que SÍ Funciona

**Variables de entorno correctas:**
```env
APP_ENV=prod
APP_SECRET=<generado-automaticamente>
APP_API_KEY=test-api-key
DATABASE_URL=postgresql://user:pass@dpg-xxxxx-internal/db
TRUSTED_PROXIES=127.0.0.1,REMOTE_ADDR
```

**Regiones compatibles:**
- Frankfurt (EU Central) ← Recomendado para Europa
- Oregon (US West)
- Ohio (US East)
- Singapore (Southeast Asia)

**Lo más importante:** Database y Web Service en la **MISMA REGIÓN**.

---

**¿Sigue sin funcionar?** Comparte el error exacto de los logs y te ayudo paso a paso.
