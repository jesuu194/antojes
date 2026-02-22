# 🚀 Guía Completa de Deployment a Render

## Resumen de la Aplicación

**Chat Geolocalizado API** es una API REST construida con Symfony 7 que permite:
- Autenticación con JWT y API Key
- Chat general y privado
- Geolocalización de usuarios (filtro de 5km con Haversine)
- Sistema de seguimiento y bloqueo de usuarios
- Solicitudes de amistad

## Prerrequisitos

1. **Cuenta en Render.com** (gratis)
   - Crear en: https://dashboard.render.com

2. **GitHub Repository** (gratis)
   - Crear un repositorio privado o público
   - Clonar y configurar este proyecto

3. **Git instalado** en tu máquina local

## Paso 1: Preparar el Projeto para Producción

### 1.1 Actualizar las variables de entorno para producción

El archivo `.env.production` ya está creado con variables de entorno para producción.

Verificar que existe en la raíz del proyecto:
```
.env.production
```

### 1.2 Verificar dependencias (composer.json)

El archivo `composer.json` debe tener todas las dependencias necesarias:
```
composer update --no-dev --optimize-autoloader
```

### 1.3 Crear un archivo `.gitignore` apropiado

Verificar que el `.gitignore` excluye:
```
.env.local
.env.*.local
/var/*.db
/var/cache/*
/vendor/
node_modules/
/public/bundles/
```

## Paso 2: Subir a GitHub

### 2.1 Crear un repositorio en GitHub

1. Ir a https://github.com/new
2. Crear repositorio `chat-geolocalizado-api`
3. No inicializar con README (opcional)

### 2.2 Configurar repositorio local

```bash
# En la carpeta del proyecto
git init
git add .
git commit -m "Initial commit: Chat Geolocalizado API"
git branch -M main
git remote add origin https://github.com/TU_USUARIO/chat-geolocalizado-api.git
git push -u origin main
```

> **Nota:** Reemplaza `TU_USUARIO` con tu usuario de GitHub

### 2.3 Verificar que se subió correctamente

Ir a https://github.com/TU_USUARIO/chat-geolocalizado-api y verificar que está el código

## Paso 3: Deployment en Render

### Opción A: Usar render.yaml (Recomendado)

#### 3A.1 Conectar GitHub a Render

1. Ir a https://dashboard.render.com
2. Click en **"+ New"** → **"Web Service"**
3. Seleccionar **"Deploy an existing repository from GitHub"**
4. Conectar tu cuenta GitHub (si no está conectada)
5. Seleccionar el repositorio `chat-geolocalizado-api`
6. Click en **"Connect"**

#### 3A.2 Configurar el servicio

En el formulario de Render:

**Configuración básica:**
- **Name:** `chat-geolocalizado-api`
- **Runtime:** PHP
- **Build Command:** `composer install --no-dev --optimize-autoloader && php bin/console cache:warmup --env=prod`
- **Start Command:** `php -S 0.0.0.0:$PORT -t public`

**Configuración de Plan:**
- **Plan:** Free (o Starter si lo prefieres)

**Environment Variables:**
Agregar las siguientes variables manualmente:

```
APP_ENV=prod
APP_DEBUG=0
APP_SECRET=<generar_aleatoriamente>
APP_API_KEY=test-api-key
DEFAULT_URI=https://<tu-servicio>.onrender.com
DATABASE_URL=postgresql://<user>:<password>@<host>:5432/<db>
```

> **Para DATABASE_URL:** Vamos a agregarlo en el siguiente paso con PostgreSQL

#### 3A.3 Crear base de datos PostgreSQL en Render

1. En el Dashboard de Render, click en **"+ New"** → **"PostgreSQL"**
2. Configurar:
   - **Name:** `chat-geolocalizado-db`
   - **Database:** `chat_geolocalizado`
   - **User:** `postgres` (o elegir otro)
   - **Plan:** Free

3. Copiar la **External Database URL** que genera

4. Volver al servicio web y agregar estas variables de entorno:
   ```
   DATABASE_URL=<la_url_que_copiaste>
   ```

#### 3A.4 Deploy

1. Click en **"Create Web Service"** o **"Deploy"**
2. Render automáticamente:
   - Clona el repositorio
   - Instala dependencias (PHP Composer)
   - Ejecuta migraciones (release command)
   - Inicia el servidor

3. Esperar a que diga **"Your service is live"**

### Opción B: Configuración manual sin render.yaml

Si prefieres configurar todo manualmente en el Dashboard de Render:

1. Crear Web Service (mismo paso 3A.1-3A.2)
2. Las variables de entorno se pueden agregar directamente en el Dashboard
3. Las migraciones se pueden ejecutar manualmente después del deploy

## Paso 4: Verificar el Deploy

### 4.1 Acceder a la aplicación

Una vez que Render diga que el servicio está "live":

1. Abrir: `https://<tu-servicio>.onrender.com/test.html`
2. Debería cargar la interfaz de prueba con documentación por endpoint
3. Debería hacer auto-login automáticamente

### 4.2 Probar endpoints

En la interfaz HTML:
- Los botones de endpoints deberían estar habilitados
- Hacer click en "Usuarios" por ejemplo
- Ver la documentación del endpoint en el panel derecho
- Ver el resultado de la petición en "Resultado"

### 4.3 Probar con Postman

Si has guardado la colección Postman:
1. Cambiar la variable `base_url` de `http://127.0.0.1:8000` a `https://<tu-servicio>.onrender.com`
2. Ejecutar requests
3. Todos deberían retornar 200 o 201

### 4.4 Ver logs

En Render Dashboard:
1. Click en el servicio
2. Tab "Logs" para ver lo que está pasando
3. Si hay errores, aparecerán aquí

## Paso 5: Configuración Adicional (Opcional)

### 5.1 Dominio personalizado

1. En el panel de Render, ir a Settings del servicio
2. Scroll a "Custom Domain"
3. Agregar tu dominio (ej: `api.midominio.com`)
4. Configurar DNS en tu provedor de dominios

### 5.2 Auto-deploy en cada push

Por defecto, Render redeploy automáticamente cuando haces push a `main`:
1. Haces `git push`
2. GitHub notifica a Render
3. Render automáticamente rebuilds y deploys

Puedes desactivar esto en Settings → "Auto-Deploy" si lo prefieres.

## Troubleshooting

### Error: "composer: command not found"

Render ya tiene PHP y Composer instalados. Si ocurre:
1. Verificar que `composer.json` existe en la raíz
2. Verificar que las dependencias están correctas

### Error: Database connection refused

1. Verificar que la DATABASE_URL está configurada correctamente
2. Verificar que la base de datos PostgreSQL está creada en Render
3. Ir a Logs para ver el error exacto

### Error: Public directory not found

Asegurarse que:
1. El Start Command es: `php -S 0.0.0.0:$PORT -t public`
2. Existe la carpeta `public/` en la raíz

### Puerta incorrecta o cambios no aplican

1. Git push de nuevo
2. En Render, click "Manual Deploy" si es necesario
3. Ver Logs mientras se está deployando

## ¿Qué sucede en el Deploy?

1. **Render clona el repositorio** desde GitHub
2. **Instala PHP Composer** y dependencias: `composer install --no-dev`
3. **Genera cache de Symfony:** `php bin/console cache:warmup --env=prod`
4. **Ejecuta migraciones:** `php bin/console doctrine:migrations:migrate`
5. **Inicia el servidor:** `php -S 0.0.0.0:PORT -t public`
6. **Asigna una URL públic** como `https://chat-geolocalizado-api.onrender.com`

## URLs Importantes

| Recurso | URL |
|---------|-----|
| Dashboard Render | https://dashboard.render.com |
| API (test.html) | https://<tu-servicio>.onrender.com/test.html |
| API (base) | https://<tu-servicio>.onrender.com/api |
| Logs en vivo | Dashboard → Logs |
| Settings | Dashboard → Settings |

## Credenciales de Prueba

Las fixtures de la base de datos crean estos usuarios automáticamente:

- **Email:** user1@example.com
- **Password:** password
- **API Key:** test-api-key (en header X-API-KEY)

## Documentación de Endpoints

La documentación está integrada en la interfaz HTML: `/test.html`

Cada endpoint tiene:
- Descripción completa
- Tipo de autenticación requerida
- Parámetros y body
- Ejemplo de respuesta

## Próximos Pasos

1. ✅ Deploy en Render
2. 📊 Monitorear logs y performance
3. 🔧 Hacer cambios en el código y hacer push (auto-deploys)
4. 📈 Escalar si es necesario (cambiar el plan de Free a Starter/Standard)

## Soporte

Si algo no funciona:
1. Revisar Logs en Render Dashboard
2. Verificar que las variables de entorno están configuradas
3. Verificar que la base de datos está creada
4. Intentar "Manual Deploy" nuevamente

¡Éxito en tu deployment! 🎉
