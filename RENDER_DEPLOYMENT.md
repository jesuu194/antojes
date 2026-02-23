# 🚀 Guía de Deployment en Render

## Paso 1: Conectar el Repositorio

1. Ve a [Render Dashboard](https://dashboard.render.com/)
2. Click en **"New +"** → **"Web Service"**
3. Conecta tu repositorio de GitHub: `https://github.com/jesuu194/antojes.git`
4. Autoriza a Render para acceder al repositorio

## Paso 2: Configurar el Servicio

### Configuración Básica:
- **Name**: `antojes-api` (o el nombre que prefieras)
- **Region**: Frankfurt (EU Central) o el más cercano a ti
- **Branch**: `main`
- **Runtime**: Docker
- **Instance Type**: Free

### Build & Deploy:
Render detectará automáticamente el `Dockerfile` y `render.yaml`

## Paso 3: Variables de Entorno

Configura estas variables de entorno en Render:

### Variables Requeridas:

```bash
APP_ENV=prod
APP_SECRET=<genera_un_secret_aleatorio_largo>
APP_API_KEY=test-api-key
DATABASE_URL=<se_genera_automaticamente>
TRUSTED_PROXIES=127.0.0.1,REMOTE_ADDR
```

### Generar APP_SECRET:
Puedes generar un secret seguro con:
```bash
php -r "echo bin2hex(random_bytes(32));"
```

## Paso 4: Configurar Base de Datos PostgreSQL

1. En el Dashboard de Render, crea una **PostgreSQL Database**:
   - Click en **"New +"** → **"PostgreSQL"**
   - **Name**: `antojes-db`
   - **Region**: El mismo que tu web service
   - **Plan**: Free
   
2. Una vez creada, copia la **Internal Database URL**

3. En tu Web Service:
   - Ve a **Environment** → **Add Environment Variable**
   - **Key**: `DATABASE_URL`
   - **Value**: Pega la URL interna de PostgreSQL

## Paso 5: Deploy

1. Click en **"Create Web Service"**
2. Render comenzará a:
   - Clonar el repositorio
   - Construir la imagen Docker
   - Ejecutar migraciones de base de datos
   - Iniciar el servicio

## Paso 6: Post-Deployment

### Cargar Datos Iniciales (Fixtures)

Una vez desplegado, ejecuta desde el Shell de Render:

```bash
php bin/console doctrine:fixtures:load --no-interaction
```

Para acceder al Shell:
1. Ve a tu Web Service en Render
2. Click en **"Shell"** en el menú lateral
3. Ejecuta el comando arriba

### Verificar el Servicio

Tu API estará disponible en:
```
https://antojes-api.onrender.com
```

### Probar el API:

```bash
# Health check
curl https://antojes-api.onrender.com/api/health

# Login
curl -X POST https://antojes-api.onrender.com/api/login \
  -H "X-API-KEY: test-api-key" \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password123"}'
```

## 🔧 Configuración Adicional

### Dominio Personalizado (Opcional)
1. En tu Web Service → **Settings** → **Custom Domain**
2. Agrega tu dominio y sigue las instrucciones de DNS

### Logs y Monitoreo
- **Logs**: Disponibles en tiempo real en el Dashboard
- **Metrics**: CPU, memoria, y requests en la pestaña "Metrics"

### Auto-Deploy
Render hace auto-deploy automáticamente cuando haces push a `main`:
```bash
git add .
git commit -m "Nuevo cambio"
git push origin main
```

## 📊 Límites del Plan Free

- **750 horas/mes** de runtime (suficiente para un proyecto)
- **512 MB RAM**
- El servicio se "duerme" después de 15 min de inactividad
- Primera request después de dormir tarda ~30 segundos

## 🐛 Troubleshooting

### Si el servicio no inicia:
1. Revisa los **Logs** en el Dashboard
2. Verifica que todas las variables de entorno estén configuradas
3. Asegúrate de que DATABASE_URL esté correcta

### Si hay errores de base de datos:
```bash
# Desde el Shell de Render:
php bin/console doctrine:schema:drop --force
php bin/console doctrine:schema:create
php bin/console doctrine:fixtures:load --no-interaction
```

### Si el servicio se queda "durmiendo":
- El plan free duerme el servicio tras inactividad
- La primera request lo despierta (tarda ~30 seg)
- Para evitarlo, usa un servicio de "ping" gratuito

## 📚 Recursos

- [Render Docs](https://render.com/docs)
- [Symfony on Render](https://render.com/docs/deploy-symfony)
- [PostgreSQL on Render](https://render.com/docs/databases)

## ✅ Checklist de Deployment

- [ ] Repositorio conectado a Render
- [ ] Web Service creado con Docker runtime
- [ ] PostgreSQL database creada
- [ ] Variables de entorno configuradas
- [ ] DATABASE_URL vinculada a PostgreSQL
- [ ] Servicio desplegado exitosamente
- [ ] Fixtures cargadas
- [ ] API probada y funcionando

---

**¡Tu API de chat geolocalizado está lista para producción! 🎉**
