# Documentación de la API REST - Chat Geolocalizado (Antojes)

## 📋 Resumen Ejecutivo

**Proyecto:** Chat Geolocalizado - API REST  
**Tecnología:** Symfony 6/7, Doctrine ORM, MySQL  
**Seguridad:** API Key + JWT (stateless)  
**Estado:** ✅ Completamente implementado según especificación

---

## 🔐 Autenticación y Seguridad

### Validación de API Key
- **Header requerido:** `X-API-KEY`
- **Ubicación:** Implementado en `src/EventSubscriber/ApiKeySubscriber.php`
- **Validación:** Automática en todas las rutas `/api`
- **Configuración:** `.env` → `APP_API_KEY=test-api-key`

### JWT (JSON Web Tokens)
- **Ubicación:** `src/Service/JwtService.php`
- **Duración:** 1 hora
- **Formato:** `Bearer <token>` en header `Authorization`
- **Claims:** `user_id`, `email`
- **Autenticador:** `src/Security/JwtAuthenticator.php`

### Headers requeridos por tipo de endpoint
```
Públicos:    X-API-KEY
Privados:    X-API-KEY + Authorization: Bearer <token>
```

---

## 📡 Endpoints de la API

### 1. **Gestión de Usuarios** (`/api/usuarios`)

#### POST - Crear usuario (público)
```http
POST /api/usuarios
X-API-KEY: test-api-key
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "securePassword123"
}
```
**Respuesta (201):**
```json
{
  "message": "User created",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com"
  }
}
```

#### GET - Listar usuarios (privado)
```http
GET /api/usuarios
X-API-KEY: test-api-key
Authorization: Bearer <token>
```

#### GET - Obtener usuario (privado)
```http
GET /api/usuarios/{id}
X-API-KEY: test-api-key
Authorization: Bearer <token>
```

#### PUT - Actualizar usuario (privado)
```http
PUT /api/usuarios/{id}
X-API-KEY: test-api-key
Authorization: Bearer <token>
Content-Type: application/json

{
  "name": "Jane Doe",
  "email": "jane@example.com"
}
```

#### DELETE - Eliminar usuario (privado)
```http
DELETE /api/usuarios/{id}
X-API-KEY: test-api-key
Authorization: Bearer <token>
```

---

### 2. **Autenticación**

#### POST - Login (público)
```http
POST /api/login
X-API-KEY: test-api-key
Content-Type: application/json

{
  "email": "user1@example.com",
  "password": "password"
}
```
**Respuesta (200):**
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "user": {
    "id": 1,
    "name": "User One",
    "email": "user1@example.com"
  }
}
```

#### POST - Logout (privado)
```http
POST /api/logout
X-API-KEY: test-api-key
Authorization: Bearer <token>
```

---

### 3. **Home y Perfil**

#### GET - Home (Datos usuario + usuarios cercanos)
```http
GET /api/home
X-API-KEY: test-api-key
Authorization: Bearer <token>
```
**Respuesta (200):**
```json
{
  "user": {
    "id": 1,
    "name": "User One",
    "email": "user1@example.com",
    "lat": "40.7128",
    "lng": "-74.0060",
    "online": true
  },
  "nearby_users": [
    {
      "id": 2,
      "name": "User Two",
      "distance": 0.12
    }
  ]
}
```
**Reglas:**
- Solo devuelve usuarios dentro de 5 km (fórmula de Haversine)
- Excluye usuarios bloqueados

#### GET - Perfil
```http
GET /api/perfil
X-API-KEY: test-api-key
Authorization: Bearer <token>
```

#### POST - Actualizar ubicación
```http
POST /api/actualizar
X-API-KEY: test-api-key
Authorization: Bearer <token>
Content-Type: application/json

{
  "lat": "40.7150",
  "lng": "-74.0080"
}
```

---

### 4. **Chat General**

#### GET - Obtener chat general y mensajes
```http
GET /api/general
X-API-KEY: test-api-key
Authorization: Bearer <token>
```
**Respuesta (200):**
```json
{
  "chat": {
    "id": 1,
    "type": "general",
    "is_active": true
  },
  "messages": [
    {
      "id": 1,
      "user_id": 1,
      "user_name": "User One",
      "text": "Hello everyone!",
      "created_at": "2026-01-29 10:30:45"
    }
  ]
}
```

#### GET/POST - Mensajes (en chat general)
```http
GET /api/mensaje?chat_id=1
X-API-KEY: test-api-key
Authorization: Bearer <token>
```

```http
POST /api/mensaje?chat_id=1
X-API-KEY: test-api-key
Authorization: Bearer <token>
Content-Type: application/json

{
  "text": "Mi mensaje"
}
```

---

### 5. **Chats Privados**

#### POST - Crear/obtener chat privado (invitación)
```http
POST /api/invitar
X-API-KEY: test-api-key
Authorization: Bearer <token>
Content-Type: application/json

{
  "user_id": 2
}
```
**Respuesta (201 o 200):**
```json
{
  "message": "Chat created",
  "chat": {
    "id": 2,
    "type": "private",
    "is_active": true
  }
}
```
**Reglas:**
- Si ya existe chat activo entre ambos usuarios, lo reutiliza
- No permite invitación si existe bloqueo

#### GET - Listar chats privados activos
```http
GET /api/privado
X-API-KEY: test-api-key
Authorization: Bearer <token>
```

#### POST - Cambiar chat activo
```http
POST /api/privado/cambiar/chat
X-API-KEY: test-api-key
Authorization: Bearer <token>
Content-Type: application/json

{
  "chat_id": 2
}
```

#### POST - Salir del chat privado
```http
POST /api/privado/salir
X-API-KEY: test-api-key
Authorization: Bearer <token>
Content-Type: application/json

{
  "chat_id": 2
}
```
**Reglas:**
- Marca `leftAt` en ChatMember
- Si ambos usuarios han salido, marca chat como `is_active = false`

---

### 6. **Funcionalidades Sociales**

#### **Bloquear/Desbloquear**

POST - Bloquear usuario
```http
POST /api/bloquear
X-API-KEY: test-api-key
Authorization: Bearer <token>
Content-Type: application/json

{
  "user_id": 2
}
```

DELETE - Desbloquear usuario
```http
DELETE /api/bloquear/2
X-API-KEY: test-api-key
Authorization: Bearer <token>
```

**Efecto del bloqueo:**
- Usuario bloqueado no aparece en `/api/home`
- No se puede crear chat privado si existe bloqueo
- En `/api/mensaje` se rechaza si hay bloqueo

---

#### **Seguir/Dejar de seguir**

POST - Seguir usuario
```http
POST /api/seguir
X-API-KEY: test-api-key
Authorization: Bearer <token>
Content-Type: application/json

{
  "user_id": 2
}
```

DELETE - Dejar de seguir
```http
DELETE /api/seguir/2
X-API-KEY: test-api-key
Authorization: Bearer <token>
```

**Características:**
- Relación unilateral (A sigue a B, pero no requiere reciprocidad)
- No se puede seguir si existe bloqueo activo

---

#### **Solicitudes de Amistad**

POST - Solicitar amistad
```http
POST /api/amistad/solicitar
X-API-KEY: test-api-key
Authorization: Bearer <token>
Content-Type: application/json

{
  "user_id": 2
}
```

POST - Aceptar solicitud
```http
POST /api/amistad/aceptar
X-API-KEY: test-api-key
Authorization: Bearer <token>
Content-Type: application/json

{
  "request_id": 1
}
```

POST - Rechazar solicitud
```http
POST /api/amistad/rechazar
X-API-KEY: test-api-key
Authorization: Bearer <token>
Content-Type: application/json

{
  "request_id": 1
}
```

GET - Listar amigos (amistad aceptada)
```http
GET /api/amistad
X-API-KEY: test-api-key
Authorization: Bearer <token>
```

**Estados de solicitud:**
- `pending`: Aguardando respuesta
- `accepted`: Amistad confirmada
- `rejected`: Rechazada
- `cancelled`: Cancelada

---

## 🗄️ Modelo de Datos

### Entidad: User
```php
- id: int (PK)
- name: string
- email: string (unique)
- password: string (hasheada)
- lat: decimal (nullable)
- lng: decimal (nullable)
- online: boolean (default: false)
- createdAt: datetime
```

### Entidad: Chat
```php
- id: int (PK)
- type: string ('general' o 'private')
- isActive: boolean (default: true)
- createdAt: datetime
```

### Entidad: ChatMember
```php
- id: int (PK)
- chat_id: int (FK)
- user_id: int (FK)
- leftAt: datetime (nullable)
```

### Entidad: Message
```php
- id: int (PK)
- chat_id: int (FK)
- user_id: int (FK)
- text: text
- createdAt: datetime
```

### Entidad: UserBlock
```php
- id: int (PK)
- blocker_user_id: int (FK)
- blocked_user_id: int (FK)
- createdAt: datetime
- Unique: (blocker_user_id, blocked_user_id)
```

### Entidad: UserFollow
```php
- id: int (PK)
- follower_user_id: int (FK)
- followed_user_id: int (FK)
- createdAt: datetime
- Unique: (follower_user_id, followed_user_id)
```

### Entidad: FriendRequest
```php
- id: int (PK)
- sender_user_id: int (FK)
- receiver_user_id: int (FK)
- status: string ('pending', 'accepted', 'rejected', 'cancelled')
- createdAt: datetime
- respondedAt: datetime (nullable)
```

---

## 🛠️ Controladores Implementados

| Archivo | Endpoints |
|---------|-----------|
| `UserController.php` | `/api/usuarios` (CRUD) |
| `LoginController.php` | `/api/login` |
| `LogoutController.php` | `/api/logout` |
| `HomeController.php` | `/api/home` |
| `GeneralController.php` | `/api/general` |
| `MessageController.php` | `/api/mensaje` |
| `ProfileController.php` | `/api/perfil` |
| `UpdateController.php` | `/api/actualizar` |
| `PrivateController.php` | `/api/privado` |
| `PrivateChatController.php` | `/api/privado/cambiar/chat`, `/api/privado/salir` |
| `BlockController.php` | `/api/bloquear` |
| `FollowController.php` | `/api/seguir` |
| `FriendshipController.php` | `/api/amistad/*` |

---

## 📚 Repositorios Disponibles

- `UserRepository.php` - Incluye método `findNearby()` con Haversine
- `ChatRepository.php`
- `ChatMemberRepository.php`
- `MessageRepository.php`
- `UserBlockRepository.php`
- `UserFollowRepository.php`
- `FriendRequestRepository.php`

---

## 🧪 Datos de Prueba (Fixtures)

Se han cargado automáticamente en `/src/DataFixtures/AppFixtures.php`:

**Usuarios:**
- user1@example.com / password (lat: 40.7128, lng: -74.0060)
- user2@example.com / password (lat: 40.7129, lng: -74.0061)
- user3@example.com / password (lat: 40.7130, lng: -74.0062)
- user4@example.com / password (lat: 41.0000, lng: -75.0000) - Fuera de 5km

**Relaciones:**
- Chat General (ID=1) con 4 usuarios
- Chat Privado entre user1 y user2
- user1 sigue a user2
- user2 sigue a user3
- user1 y user3 son amigos
- Solicitud de amistad pendiente: user4 → user2
- Bloqueo: user3 bloquea a user4

---

## ✅ Códigos de Respuesta HTTP

| Código | Significado |
|--------|------------|
| 200 | OK - Operación exitosa |
| 201 | Created - Recurso creado |
| 400 | Bad Request - Datos inválidos |
| 401 | Unauthorized - API Key o token inválido |
| 403 | Forbidden - Acceso denegado (no miembro del chat) |
| 404 | Not Found - Recurso no encontrado |
| 500 | Server Error |

---

## 🚀 Ejemplo de Flujo Completo

### 1. Crear usuario
```bash
curl -X POST http://localhost:8000/api/usuarios \
  -H "X-API-KEY: test-api-key" \
  -H "Content-Type: application/json" \
  -d '{"name":"Test User","email":"test@example.com","password":"pass123"}'
```

### 2. Login
```bash
curl -X POST http://localhost:8000/api/login \
  -H "X-API-KEY: test-api-key" \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"pass123"}'
```

### 3. Obtener home
```bash
curl -X GET http://localhost:8000/api/home \
  -H "X-API-KEY: test-api-key" \
  -H "Authorization: Bearer <token>"
```

### 4. Enviar mensaje al chat general
```bash
curl -X POST http://localhost:8000/api/mensaje?chat_id=1 \
  -H "X-API-KEY: test-api-key" \
  -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/json" \
  -d '{"text":"¡Hola a todos!"}'
```

---

## 🔍 Configuración

### `.env`
```env
APP_ENV=dev
APP_SECRET=some_random_secret_key_here
APP_API_KEY=test-api-key
DATABASE_URL="sqlite:///./var/data.db"
```

### `config/services.yaml`
- `app_secret`: Clave para JWT
- `app_api_key`: Clave API para validación

### `src/EventSubscriber/ApiKeySubscriber.php`
Valida `X-API-KEY` en todas las rutas `/api` automáticamente

---

## 📝 Notas Importantes

1. **Geolocalización:** Usa fórmula de Haversine para calcular distancias
2. **Chat General:** Siempre tiene ID=1
3. **Chats Privados:** Se reutilizan si existe uno activo entre dos usuarios
4. **Bloqueos:** Se aplican bilateralmente a nivel lógico
5. **JWT:** Stateless, válido por 1 hora
6. **Seguridad:** Doble validación: API Key + JWT para endpoints privados

---

## 🎯 Mejoras Futuras

- WebSockets/Mercure para mensajería en tiempo real
- Notificaciones push
- Moderación de contenido
- Archivos multimedia (imágenes, audio)
- Índices espaciales para geolocalización
- Auditoría de acciones sensibles

---

**Estado:** ✅ Completamente implementado  
**Última actualización:** 29/01/2026  
**Servidor:** http://127.0.0.1:8000
