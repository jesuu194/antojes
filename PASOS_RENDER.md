# 🎯 PASOS RÁPIDOS PARA DEPLOY EN RENDER

## ✅ Código ya subido a GitHub
- Repositorio: https://github.com/jesuu194/antojes.git
- Branch: main
- Todo listo para deployment

## 📋 Checklist de Deployment

### 1️⃣ Crear PostgreSQL Database
1. Ve a https://dashboard.render.com/
2. Click **"New +"** → **"PostgreSQL"**
3. Configuración:
   - **Name**: `antojes-db`
   - **Database**: `antojes_db`
   - **User**: `antojes_user` (o déjalo auto)
   - **Region**: Frankfurt (EU Central) 
   - **Plan**: **Free**
4. Click **"Create Database"**
5. **IMPORTANTE**: Copia la **"Internal Database URL"** (la necesitarás)

### 2️⃣ Crear Web Service
1. Click **"New +"** → **"Web Service"**
2. Click **"Build and deploy from a Git repository"**
3. Conecta tu cuenta de GitHub si no lo has hecho
4. Busca y selecciona: `jesuu194/antojes`
5. Click **"Connect"**

### 3️⃣ Configurar Web Service

**Información Básica:**
- **Name**: `antojes-api`
- **Region**: Frankfurt (EU Central) - mismo que la BD
- **Branch**: `main`
- **Root Directory**: (dejar vacío)

**Build Settings:**
- **Runtime**: Docker
- Render detectará automáticamente el `Dockerfile`

**Plan:**
- Selecciona: **Free**

### 4️⃣ Variables de Entorno

Click en **"Advanced"** y agrega estas variables:

```
APP_ENV = prod
APP_SECRET = <GENERAR-UNO-ALEATORIO>
APP_API_KEY = test-api-key
DATABASE_URL = <PEGAR-LA-INTERNAL-URL-DE-POSTGRESQL>
TRUSTED_PROXIES = 127.0.0.1,REMOTE_ADDR
```

**Para generar APP_SECRET:**
Ejecuta en tu PowerShell local:
```powershell
-join ((48..57) + (65..90) + (97..122) | Get-Random -Count 32 | % {[char]$_})
```

**DATABASE_URL:**
- Usa la **Internal Database URL** que copiaste en el paso 1
- Ejemplo: `postgresql://user:password@dpg-xxxxx-internal/database`

### 5️⃣ Crear el Servicio

1. Click **"Create Web Service"**
2. Render comenzará a:
   - ✅ Clonar el repositorio
   - ✅ Construir la imagen Docker
   - ✅ Ejecutar migraciones
   - ✅ Cargar datos de ejemplo (fixtures)
   - ✅ Iniciar el servidor

⏱️ Esto toma aproximadamente **3-5 minutos**

### 6️⃣ Verificar el Deployment

Una vez que el estado sea **"Live"**, tu API estará en:
```
https://antojes-api.onrender.com
```

**Probar la API:**

```bash
# 1. Health Check
curl https://antojes-api.onrender.com/api/health

# 2. Login
curl -X POST https://antojes-api.onrender.com/api/login \
  -H "X-API-KEY: test-api-key" \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password123"}'

# 3. Home (necesitas el token del paso 2)
curl https://antojes-api.onrender.com/api/home \
  -H "X-API-KEY: test-api-key" \
  -H "Authorization: Bearer <TU-TOKEN>"
```

## 🔍 Si algo falla...

### Ver los Logs:
1. En tu Web Service, click en **"Logs"**
2. Busca errores en rojo

### Problemas comunes:

**"Database connection failed"**
- Verifica que DATABASE_URL sea la **Internal URL**
- Asegúrate de que la base de datos esté en la misma región

**"Port already in use"**
- No hagas nada, Render lo maneja automáticamente

**"Migrations failed"**
- Ve al Shell de Render (botón "Shell")
- Ejecuta manualmente:
  ```bash
  php bin/console doctrine:schema:create
  php bin/console doctrine:fixtures:load --no-interaction
  ```

### Ejecutar comandos manualmente:
1. En tu Web Service → **"Shell"** (menú lateral)
2. Ejecutar:
   ```bash
   # Ver estado de la BD
   php bin/console doctrine:schema:validate
   
   # Recargar datos
   php bin/console doctrine:fixtures:load --no-interaction
   
   # Ver usuarios
   php bin/console doctrine:query:sql "SELECT * FROM \"user\" LIMIT 5"
   ```

## 📱 Usar con Postman

1. Importa: `postman_collection_updated.json`
2. Edita la variable `baseUrl`: `https://antojes-api.onrender.com`
3. Variable `apiKey`: `test-api-key`
4. Haz login → el token se guarda automáticamente
5. ¡Prueba todos los endpoints!

## 🔄 Auto-Deploy

Cada vez que hagas push a `main`, Render hará deploy automáticamente:

```bash
# Hacer cambios en local
git add .
git commit -m "Nuevo cambio"
git push origin main

# Render detecta el push y hace deploy automático
```

## ⚡ Importante sobre el Plan Free

- El servicio se "duerme" después de 15 min sin actividad
- Primera request después de dormir: ~30 segundos
- Perfecto para desarrollo y pruebas
- Para mantenerlo "despierto": usa un servicio de ping

## 🎉 ¡Listo!

Tu API de chat geolocalizado está en producción con:
- ✅ 24 endpoints funcionando
- ✅ Autenticación JWT
- ✅ Base de datos PostgreSQL
- ✅ 21 usuarios de prueba
- ✅ Mensajes en español
- ✅ HTTPS automático
- ✅ Auto-deploy desde GitHub

---

**URL de tu API**: https://antojes-api.onrender.com

**Repositorio**: https://github.com/jesuu194/antojes.git

**Documentación completa**: Ver `RENDER_DEPLOYMENT.md`
