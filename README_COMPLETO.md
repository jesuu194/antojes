# 📚 Chat Geolocalizado - Documentación Completa

## 🚀 Inicio Rápido

### Requisitos
- PHP 8.2+
- Composer
- SQLite
- Postman (para testing)

### Instalación

```bash
# 1. Clonar/descargar proyecto
cd c:\xampp\htdocs\antojes

# 2. Instalar dependencias
composer install

# 3. Cargar fixtures (datos de prueba)
php bin/console doctrine:fixtures:load --no-interaction

# 4. Iniciar servidor
php -S 127.0.0.1:8000 -t public
```

### Acceso
- **Frontend**: http://127.0.0.1:8000/app.html
- **API**: http://127.0.0.1:8000/api

---

## 🔐 Seguridad

### API Key
Todos los endpoints requieren:
```
X-API-KEY: test-api-key
```

### Autenticación JWT
Endpoints protegidos requieren:
```
Authorization: Bearer {token}
```

El token se obtiene al hacer login y es válido por **1 hora**.

---

## 🗂️ Estructura del Proyecto

```
antojes/
├── src/
│   ├── Controller/        (13 controladores con 30+ endpoints)
│   ├── Entity/           (7 entidades)
│   ├── Repository/       (7 repositorios)
│   ├── Service/          (JwtService)
│   ├── EventSubscriber/  (API Key, CORS)
│   └── DataFixtures/     (Datos de prueba)
├── config/              (Configuración Symfony)
├── migrations/          (Migraciones BD)
├── public/
│   ├── app.html        (Frontend SPA)
│   └── index.php       (Punto entrada)
├── var/
│   ├── cache/
│   ├── data.db         (Base de datos SQLite)
│   └── log/
└── vendor/             (Dependencias)
```

---

## 📊 Arquitectura de BD

### Entidades

```
User (id, name, email, password, lat, lng, online, createdAt)
  ├─ Chat (id, type, isActive, createdAt)
  │   ├─ ChatMember (id, chat_id, user_id, leftAt)
  │   └─ Message (id, chat_id, user_id, text, createdAt)
  ├─ UserBlock (id, blocker_user_id, blocked_user_id, createdAt)
  ├─ UserFollow (id, follower_user_id, followed_user_id, createdAt)
  └─ FriendRequest (id, sender_user_id, receiver_user_id, status, createdAt, respondedAt)
```

### Relaciones

- **User ↔ Chat**: Muchos a muchos (a través de ChatMember)
- **User ↔ UserBlock**: Uno a muchos (bloqueos)
- **User ↔ UserFollow**: Uno a muchos (seguimientos)
- **User ↔ FriendRequest**: Uno a muchos (solicitudes)

---

## 🎯 Endpoints por Categoría

### 1. Autenticación (3 endpoints)
| Método | Endpoint | Descripción |
|--------|----------|-------------|
| POST | `/login` | Autenticarse |
| POST | `/usuarios` | Crear cuenta |
| POST | `/logout` | Cerrar sesión |

### 2. Geolocalización (2 endpoints)
| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/home` | Usuarios cercanos (< 5km) |
| POST | `/actualizar` | Actualizar ubicación |

### 3. Chats (6 endpoints)
| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/general` | Obtener chat general |
| POST | `/mensaje?chat_id=X` | Enviar mensaje |
| POST | `/invitar` | Crear chat privado |
| GET | `/privado` | Listar chats privados |
| POST | `/privado/salir` | Salir de chat |
| GET | `/perfil` | Obtener perfil |

### 4. Bloqueos (3 endpoints)
| Método | Endpoint | Descripción |
|--------|----------|-------------|
| POST | `/bloquear` | Bloquear usuario |
| GET | `/bloqueados` | Listar bloqueados |
| DELETE | `/bloquear/{id}` | Desbloquear |

### 5. Seguimientos (3 endpoints)
| Método | Endpoint | Descripción |
|--------|----------|-------------|
| POST | `/seguir` | Seguir usuario |
| GET | `/seguidos` | Listar seguidos |
| DELETE | `/seguir/{id}` | Dejar de seguir |

### 6. Amistades (5 endpoints)
| Método | Endpoint | Descripción |
|--------|----------|-------------|
| POST | `/amistad/solicitar` | Enviar solicitud |
| GET | `/amistad` | Listar amigos |
| GET | `/amistad/pendientes` | Solicitudes pendientes |
| POST | `/amistad/aceptar` | Aceptar solicitud |
| POST | `/amistad/rechazar` | Rechazar solicitud |

### 7. Usuarios (4 endpoints)
| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/usuarios` | Listar usuarios |
| PUT | `/usuarios/{id}` | Actualizar usuario |
| DELETE | `/usuarios/{id}` | Eliminar usuario |
| GET | `/perfil` | Mi perfil |

---

## 🧪 Testing con Postman

### Importar Collection
1. Abre Postman
2. Click "Import"
3. Selecciona `postman_collection.json`
4. Los endpoints se cargarán automáticamente

### Environment Variables
Crear un environment con:
```json
{
  "base_url": "http://127.0.0.1:8000/api",
  "api_key": "test-api-key",
  "token": ""
}
```

El token se guarda automáticamente después de hacer login.

### Flujo de Testing Recomendado

1. **Login** (obtiene token)
2. **Home** (usuarios cercanos)
3. **Actualizar Ubicación** (cambiar lat/lng)
4. **Chat General** (enviar mensaje)
5. **Crear Chat Privado** (invitar usuario)
6. **Bloquear Usuario** (test de bloqueos)
7. **Seguir Usuario** (test de seguimientos)
8. **Amistad** (solicitar → aceptar/rechazar)

---

## 👤 Usuarios de Prueba

| Email | Contraseña | Nombre |
|-------|-----------|--------|
| user1@example.com | password | User One |
| user2@example.com | password | User Two |
| user3@example.com | password | User Three |
| user4@example.com | password | User Four |

---

## 🔄 Flujos de Uso

### Chat General
1. GET `/general` - Obtener mensajes del chat general
2. POST `/mensaje?chat_id=1` - Enviar mensaje

### Chat Privado
1. POST `/invitar` - Crear/activar chat privado
2. GET `/privado` - Listar tus chats privados
3. POST `/mensaje?chat_id=X` - Enviar mensaje privado
4. POST `/privado/salir` - Salir del chat

### Bloqueo
1. POST `/bloquear` - Bloquear usuario
2. GET `/bloqueados` - Ver bloqueados
3. DELETE `/bloquear/{id}` - Desbloquear

### Amistad
1. POST `/amistad/solicitar` - Enviar solicitud
2. GET `/amistad/pendientes` - Ver solicitudes (como receptor)
3. POST `/amistad/aceptar` - Aceptar solicitud
4. GET `/amistad` - Listar amigos

---

## 🚨 Códigos HTTP

| Código | Significado |
|--------|-------------|
| 200 | OK - Solicitud exitosa |
| 201 | Created - Recurso creado |
| 400 | Bad Request - Datos inválidos |
| 401 | Unauthorized - API Key o Token inválido |
| 404 | Not Found - Recurso no encontrado |
| 500 | Internal Server Error |

---

## 📍 Geolocalización

### Cálculo de Distancia
Se utiliza la **fórmula de Haversine**:
```
d = 2R * arcsin(sqrt(sin²((lat2-lat1)/2) + cos(lat1) * cos(lat2) * sin²((lng2-lng1)/2)))
```

### Radio de Búsqueda
- **Distancia máxima**: 5 km
- Solo usuarios dentro de 5km aparecen en `/home`

### Actualizar Ubicación
```bash
POST /api/actualizar
{
  "lat": 40.7128,
  "lng": -74.0060
}
```

---

## 🔧 Configuración

### .env
```
APP_ENV=dev
APP_SECRET=...
DATABASE_URL=sqlite:///%kernel.project_dir%/var/data.db
APP_API_KEY=test-api-key
JWT_SECRET=your-secret-key
```

### config/services.yaml
```yaml
App\EventSubscriber\CorsSubscriber:
  tags:
    - { name: kernel.event_subscriber, priority: 9999 }

App\EventSubscriber\ApiKeySubscriber:
  tags:
    - { name: kernel.event_subscriber, priority: 100 }
```

---

## 📝 Frontend

### Características
- ✅ SPA responsivo (HTML/CSS/JavaScript)
- ✅ Login y signup integrado
- ✅ Dashboard con sidebar
- ✅ Soporte para múltiples secciones
- ✅ Actualizaciones en tiempo real

### Secciones
1. **Home** - Usuarios cercanos
2. **Chat General** - Chat público
3. **Chats Privados** - Mensajes privados
4. **Bloqueados** - Usuarios bloqueados
5. **Seguidos** - Usuarios que sigues
6. **Amistades** - Amigos y solicitudes

---

## 🐛 Debugging

### Logs
```bash
# Ver logs en tiempo real
tail -f var/log/dev.log
```

### Cache
```bash
# Limpiar cache
php bin/console cache:clear
```

### Base de Datos
```bash
# Recargar fixtures
php bin/console doctrine:fixtures:load --no-interaction

# Ejecutar migraciones
php bin/console doctrine:migrations:migrate
```

---

## ✅ Checklist Final

- [x] API Key validado en todos los endpoints
- [x] JWT con 1 hora de expiración
- [x] CORS configurado (preflight OPTIONS)
- [x] 7 entidades con relaciones Doctrine
- [x] 30+ endpoints funcionales
- [x] Frontend SPA completo
- [x] Geolocalización con Haversine
- [x] Chat general y privado
- [x] Bloqueos, seguimientos, amistades
- [x] Fixtures con 4 usuarios de prueba
- [x] Documentación completa
- [x] Postman collection lista

---

## 📞 Soporte

Para cualquier duda o error, revisar:
1. Consola del navegador (F12)
2. Logs en `var/log/dev.log`
3. Terminal del servidor PHP

---

**¡Proyecto completado y listo para evaluación!** 🎉
