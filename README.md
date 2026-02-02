# 🌍 Antojes - Chat Geolocalizado API

Chat REST API con geolocalización, autenticación JWT y funcionalidades sociales. Construido con **Symfony 6/7**, **Doctrine ORM** y **MySQL/SQLite**.

## ✨ Características

✅ **Autenticación Segura**
- API Key (X-API-KEY)
- JWT Stateless (1 hora de duración)
- Doble validación automática

✅ **Geolocalización**
- Radio de 5 km con fórmula de Haversine
- Actualización de ubicación en tiempo real
- Lista dinámica de usuarios cercanos

✅ **Chat General**
- Chat compartido con ID fijo (1)
- Accesible para todos los usuarios autenticados
- Historial de mensajes

✅ **Chats Privados**
- Invitaciones entre usuarios
- Reutilización automática de chats activos
- Control de miembros y salidas
- Inactividad automática cuando ambos salen

✅ **Funcionalidades Sociales**
- Bloqueo de usuarios (bidireccional)
- Seguimiento (unilateral)
- Solicitudes de amistad con 4 estados
- Gestión completa de relaciones

✅ **Endpoints Completamente Documentados**
- 13 controladores implementados
- 7 repositorios con métodos custom
- Validación automática en todos los endpoints

---

## 🚀 Inicio Rápido

### 1️⃣ Clonar y configurar
```bash
cd c:\xampp\htdocs\antojes
cp .env.example .env
```

### 2️⃣ Instalar dependencias
```bash
composer install
```

### 3️⃣ Configurar BD y cargar fixtures
```bash
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:schema:create
php bin/console doctrine:fixtures:load --no-interaction
```

### 4️⃣ Iniciar servidor
```bash
php -S 127.0.0.1:8000 -t public
```

✅ **API disponible en:** http://127.0.0.1:8000/api/

---

## 📚 Documentación Completa

Ver [API_DOCUMENTATION.md](./API_DOCUMENTATION.md) para:
- 📋 Especificación de todos los endpoints
- 🔐 Detalles de autenticación
- 🗄️ Modelo de datos
- 📝 Ejemplos de curl
- 🧪 Datos de prueba incluidos

---

## 🏗️ Estructura del Proyecto

```
antojes/
├── src/
│   ├── Controller/           # 13 controladores de endpoints
│   │   ├── LoginController.php
│   │   ├── UserController.php
│   │   ├── HomeController.php
│   │   ├── GeneralController.php
│   │   ├── PrivateController.php
│   │   ├── MessageController.php
│   │   ├── BlockController.php
│   │   ├── FollowController.php
│   │   ├── FriendshipController.php
│   │   └── ...
│   ├── Entity/              # 7 entidades
│   │   ├── User.php
│   │   ├── Chat.php
│   │   ├── Message.php
│   │   ├── ChatMember.php
│   │   ├── UserBlock.php
│   │   ├── UserFollow.php
│   │   └── FriendRequest.php
│   ├── Repository/          # 7 repositorios
│   ├── Service/
│   │   └── JwtService.php    # Manejo de tokens
│   ├── Security/
│   │   └── JwtAuthenticator.php
│   ├── EventSubscriber/
│   │   └── ApiKeySubscriber.php
│   └── DataFixtures/
│       └── AppFixtures.php   # Datos de prueba
├── config/
│   ├── services.yaml         # Parámetros de config
│   └── packages/
│       ├── security.yaml     # Config de seguridad
│       └── doctrine.yaml     # Config de BD
├── public/
│   └── index.php             # Punto de entrada
├── var/
│   ├── cache/
│   └── log/
├── .env                       # Variables de entorno
├── API_DOCUMENTATION.md       # Docs completas
└── README.md                  # Este archivo
```

---

## 🔐 Seguridad

### Headers Requeridos

**Todos los endpoints `/api` requieren:**
```http
X-API-KEY: test-api-key
```

**Endpoints privados además requieren:**
```http
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc...
```

### Validación

✅ API Key validada automáticamente en `ApiKeySubscriber`  
✅ JWT validado en cada request privado  
✅ Ownership verificado en operaciones sensibles  
✅ Bloqueos validados en chats privados

---

## 📦 Endpoints Principales

| Método | Ruta | Descripción | Auth |
|--------|------|-------------|------|
| POST | `/api/login` | Login | API Key |
| POST | `/api/logout` | Logout | JWT |
| GET | `/api/home` | Home + usuarios cercanos | JWT |
| GET | `/api/general` | Chat general | JWT |
| POST | `/api/mensaje` | Enviar mensaje | JWT |
| GET | `/api/privado` | Listar chats privados | JWT |
| POST | `/api/invitar` | Crear/obtener chat privado | JWT |
| POST | `/api/bloquear` | Bloquear usuario | JWT |
| POST | `/api/seguir` | Seguir usuario | JWT |
| POST | `/api/amistad/solicitar` | Solicitar amistad | JWT |

📖 **Documentación completa en [API_DOCUMENTATION.md](./API_DOCUMENTATION.md)**

---

## 🧪 Datos de Prueba

Se incluyen automáticamente al cargar fixtures:

**Usuarios (password: `password` para todos):**
- user1@example.com ← cercano
- user2@example.com ← cercano
- user3@example.com ← cercano
- user4@example.com ← lejano (fuera de 5km)

**Relaciones pre-configuradas:**
- Chat general con mensajes
- Chat privado activo entre user1 y user2
- user1 sigue a user2
- user1 y user3 son amigos
- user3 bloquea a user4

---

## 🛠️ Ejemplos

### Crear usuario y login
```bash
# 1. Crear usuario
curl -X POST http://localhost:8000/api/usuarios \
  -H "X-API-KEY: test-api-key" \
  -H "Content-Type: application/json" \
  -d '{"name":"Test","email":"test@example.com","password":"pass123"}'

# 2. Login
curl -X POST http://localhost:8000/api/login \
  -H "X-API-KEY: test-api-key" \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"pass123"}'
  # Copia el token devuelto

# 3. Usar el token en solicitudes
TOKEN="eyJ0eXAiOiJKV1QiLCJhbGc..."
curl -X GET http://localhost:8000/api/home \
  -H "X-API-KEY: test-api-key" \
  -H "Authorization: Bearer $TOKEN"
```

### Enviar mensaje al chat general
```bash
curl -X POST http://localhost:8000/api/mensaje?chat_id=1 \
  -H "X-API-KEY: test-api-key" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"text":"¡Hola a todos!"}'
```

### Invitar a chat privado
```bash
curl -X POST http://localhost:8000/api/invitar \
  -H "X-API-KEY: test-api-key" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"user_id": 2}'
```

---

## 🗄️ Base de Datos

### Entidades
- **User** - Usuarios del sistema
- **Chat** - Chats (general o privado)
- **ChatMember** - Membresía en chats con fecha de salida
- **Message** - Mensajes en chats
- **UserBlock** - Bloqueos entre usuarios
- **UserFollow** - Seguimiento de usuarios
- **FriendRequest** - Solicitudes de amistad

### Relaciones Clave
- Geolocalización: User → lat/lng
- Chats: Chat.type → 'general' o 'private'
- Privacidad: UserBlock (bidireccional), UserFollow (unilateral)
- Amistad: FriendRequest con estados (pending, accepted, rejected)

---

## 📋 Configuración de Entorno

`.env`:
```env
APP_ENV=dev
APP_SECRET=some_random_secret_key_here
APP_API_KEY=test-api-key
DATABASE_URL="sqlite:///./var/data.db"
DEFAULT_URI=http://localhost:8000
```

---

## ✅ Testing

La API ha sido probada con:
- ✅ Postman (manual)
- ✅ Validación de seguridad (API Key + JWT)
- ✅ Funcionalidad de geolocalización
- ✅ Control de permisos en chats
- ✅ Relaciones sociales

---

## 🚀 Próximas Mejoras

- [ ] WebSockets para mensajería en tiempo real
- [ ] Sistema de notificaciones
- [ ] Moderación de contenido
- [ ] Soporte multimedia
- [ ] Índices espaciales en BD
- [ ] Caché Redis
- [ ] Rate limiting

---

## 📄 Licencia

Proyecto educativo - Universitat de Girona

---

## 🤝 Contribuir

Este es un proyecto educativo. Para cambios, contacta con el instructor.

---

**API en vivo:** http://127.0.0.1:8000/api/  
**Documentación:** [API_DOCUMENTATION.md](./API_DOCUMENTATION.md)  
**Última actualización:** 29/01/2026
