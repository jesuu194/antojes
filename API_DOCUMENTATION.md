# Documentación de la API REST - Chat Geolocalizado (Antojes)

## 📋 Resumen Ejecutivo

**Proyecto:** Chat Geolocalizado - API REST  
**Tecnología:** Symfony 7.2, Doctrine ORM 3.6, MySQL 8.0  
**Seguridad:** API Key + JWT (stateless)  
**Estado:** ✅ Completamente implementado con formato estandarizado `{data, error}`

## ✨ Formato de Respuesta Estandarizado

**TODAS las respuestas de la API usan el formato:**
```json
{
  "data": { ... },
  "error": null
}
```

En caso de éxito: `data` contiene la información, `error` es `null`  
En caso de error: `data` es `null`, `error` contiene el mensaje de error

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
  "data": {
    "message": "User created",
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com"
    }
  },
  "error": null
}
```

#### GET - Listar usuarios (privado)
```http
GET /api/usuarios
X-API-KEY: test-api-key
Authorization: Bearer <token>
```
**Respuesta (200):**
```json
{
  "data": {
    "users": [
      {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "lat": "40.7128",
        "lng": "-74.0060",
        "online": true
      },
      {
        "id": 2,
        "name": "Jane Smith",
        "email": "jane@example.com",
        "lat": "40.7129",
        "lng": "-74.0061",
        "online": false
      }
    ]
  },
  "error": null
}
```

#### GET - Obtener usuario (privado)
```http
GET /api/usuarios/{id}
X-API-KEY: test-api-key
Authorization: Bearer <token>
```
**Respuesta (200):**
```json
{
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "lat": "40.7128",
    "lng": "-74.0060",
    "online": true
  },
  "error": null
}
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
**Respuesta (200):**
```json
{
  "data": {
    "message": "User updated",
    "user": {
      "id": 1,
      "name": "Jane Doe",
      "email": "jane@example.com"
    }
  },
  "error": null
}
```

#### DELETE - Eliminar usuario (privado)
```http
DELETE /api/usuarios/{id}
X-API-KEY: test-api-key
Authorization: Bearer <token>
```
**Respuesta (200):**
```json
{
  "data": {
    "message": "User deleted successfully"
  },
  "error": null
}
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
  "data": {
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "user": {
      "id": 1,
      "name": "User One",
      "email": "user1@example.com"
    }
  },
  "error": null
}
```

#### POST - Logout (privado)
```http
POST /api/logout
X-API-KEY: test-api-key
Authorization: Bearer <token>
```
**Respuesta (200):**
```json
{
  "data": {
    "message": "Logout successful"
  },
  "error": null
}
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
  "data": {
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
  },
  "error": null
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
**Respuesta (200):**
```json
{
  "data": {
    "id": 1,
    "name": "User One",
    "email": "user1@example.com",
    "lat": "40.7128",
    "lng": "-74.0060",
    "online": true
  },
  "error": null
}
```

#### POST - Actualizar ubicación
```http
POST /api/actualizar
X-API-KEY: test-api-key
Authorization: Bearer <token>
Content-Type: application/json

{
  "lat": "40.7150",
  "lng": "-74.0080",
  "online": true
}
```
**Respuesta (200):**
```json
{
  "data": {
    "message": "User data updated successfully",
    "user": {
      "id": 1,
      "name": "User One",
      "email": "user1@example.com",
      "lat": "40.7150",
      "lng": "-74.0080",
      "online": true
    }
  },
  "error": null
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
  "data": {
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
        "text": "¡Hola a todos!",
        "created_at": "2026-01-29 10:30:45"
      }
    ]
  },
  "error": null
}
```

#### GET/POST - Mensajes (en chat general)
```http
GET /api/mensaje?chat_id=1
X-API-KEY: test-api-key
Authorization: Bearer <token>
```
**Respuesta (200):**
```json
{
  "data": {
    "messages": [
      {
        "id": 1,
        "user_id": 1,
        "user_name": "User One",
        "text": "¡Hola a todos!",
        "created_at": "2026-02-22 10:30:45"
      },
      {
        "id": 2,
        "user_id": 2,
        "user_name": "User Two",
        "text": "¡Hola!",
        "created_at": "2026-02-22 10:31:12"
      }
    ]
  },
  "error": null
}
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
**Respuesta (201):**
```json
{
  "data": {
    "message": "Message sent",
    "message_data": {
      "id": 3,
      "user_id": 1,
      "user_name": "User One",
      "text": "Mi mensaje",
      "created_at": "2026-02-22 10:35:00"
    }
  },
  "error": null
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
  "data": {
    "message": "Chat created",
    "chat": {
      "id": 2,
      "type": "private",
      "is_active": true
    }
  },
  "error": null
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
**Respuesta (200):**
```json
{
  "data": {
    "chats": [
      {
        "id": 2,
        "type": "private",
        "is_active": true,
        "other_user": {
          "id": 2,
          "name": "User Two",
          "email": "user2@example.com"
        }
      },
      {
        "id": 3,
        "type": "private",
        "is_active": true,
        "other_user": {
          "id": 3,
          "name": "User Three",
          "email": "user3@example.com"
        }
      }
    ]
  },
  "error": null
}
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
**Respuesta (200):**
```json
{
  "data": {
    "message": "Active chat changed successfully",
    "chat_id": 2
  },
  "error": null
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
**Respuesta (200):**
```json
{
  "data": {
    "message": "Successfully left the chat"
  },
  "error": null
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
**Respuesta (200):**
```json
{
  "data": {
    "message": "User blocked successfully"
  },
  "error": null
}
```

DELETE - Desbloquear usuario
```http
DELETE /api/bloquear/2
X-API-KEY: test-api-key
Authorization: Bearer <token>
```
**Respuesta (200):**
```json
{
  "data": {
    "message": "User unblocked successfully"
  },
  "error": null
}
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
**Respuesta (200):**
```json
{
  "data": {
    "message": "Now following user"
  },
  "error": null
}
```

DELETE - Dejar de seguir
```http
DELETE /api/seguir/2
X-API-KEY: test-api-key
Authorization: Bearer <token>
```
**Respuesta (200):**
```json
{
  "data": {
    "message": "Unfollowed user successfully"
  },
  "error": null
}
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
**Respuesta (200):**
```json
{
  "data": {
    "message": "Friend request sent",
    "request": {
      "id": 1,
      "sender_id": 1,
      "receiver_id": 2,
      "status": "pending"
    }
  },
  "error": null
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
**Respuesta (200):**
```json
{
  "data": {
    "message": "Friend request accepted",
    "request": {
      "id": 1,
      "status": "accepted"
    }
  },
  "error": null
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
**Respuesta (200):**
```json
{
  "data": {
    "message": "Friend request rejected",
    "request": {
      "id": 1,
      "status": "rejected"
    }
  },
  "error": null
}
```

GET - Listar amigos (amistad aceptada)
```http
GET /api/amistad
X-API-KEY: test-api-key
Authorization: Bearer <token>
```
**Respuesta (200):**
```json
{
  "data": {
    "friends": [
      {
        "id": 2,
        "name": "User Two",
        "email": "user2@example.com",
        "online": true
      },
      {
        "id": 3,
        "name": "User Three",
        "email": "user3@example.com",
        "online": false
      }
    ]
  },
  "error": null
}
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

1. **Formato de Respuesta:** TODAS las respuestas usan `{data, error}`
2. **Geolocalización:** Usa fórmula de Haversine para calcular distancias
3. **Chat General:** Siempre tiene ID=1
4. **Chats Privados:** Se reutilizan si existe uno activo entre dos usuarios
5. **Bloqueos:** Se aplican bilateralmente a nivel lógico
6. **JWT:** Stateless, válido por 1 hora
7. **Seguridad:** Doble validación: API Key + JWT para endpoints privados
8. **Nomenclatura:** Endpoints en español excepto login, logout y home

---

## 🎯 Mejoras Futuras

- WebSockets/Mercure para mensajería en tiempo real
- Notificaciones push
- Moderación de contenido
- Archivos multimedia (imágenes, audio)
- Índices espaciales para geolocalización
- Auditoría de acciones sensibles

---

**Estado:** ✅ Completamente implementado con formato `{data, error}`  
**Última actualización:** 22/02/2026  
**Servidor Local:** http://localhost:8000  
**Interfaz Web:** http://localhost:8000/index.html

---

## 📁 Gestión del Proyecto

### 1. Iniciación y Planificación del Proyecto

Es la parte más creativa del proceso: como un folio en blanco donde se esbozan las partes del proyecto y de donde emerge una idea clara sobre qué será el producto final.

- **Definición del Alcance:** La necesidad de partida era disponer de una red social de chat donde los usuarios solo puedan comunicarse con personas físicamente cercanas (radio de 5 km), sin exponer datos de ubicación exacta. El proyecto se acotó a una **API REST** consumible desde cualquier cliente, dejando fuera del alcance inicial funcionalidades como notificaciones push o mensajería en tiempo real (WebSockets/Mercure), que quedan recogidas como mejoras futuras.
- **Análisis de Viabilidad:** Técnicamente viable con herramientas open-source (Symfony, Doctrine, MySQL/PostgreSQL) sin coste de licencias. El coste económico se limita al hosting: las plataformas Railway y Render ofrecen tier gratuito suficiente para un proyecto académico, sin necesidad de dominio propio (se usan los subdominios `*.railway.app` / `*.onrender.com`).
- **Planificación Temporal:** El desarrollo se organizó en tres fases: (1) modelado de la base de datos y entidades, (2) implementación de controladores y seguridad, (3) pruebas y despliegue.
- **Asignación de Recursos y Roles:** Proyecto individual. El desarrollador asumió todos los roles: análisis, backend, frontend, pruebas y despliegue.
- **Especificación de Requisitos principales:**
  - Los usuarios deben poder registrarse, autenticarse y actualizar su posición GPS.
  - Solo deben ver y chatear con usuarios en un radio de 5 km.
  - Debe existir un chat general y chats privados entre dos usuarios.
  - Los usuarios pueden seguirse, enviar solicitudes de amistad y bloquearse entre sí.
  - Toda comunicación con la API debe estar protegida por API Key y JWT.

---

### 2. Análisis y Diseño

#### Diseño de la Base de Datos

Se definieron 7 entidades Doctrine que mapean directamente a las tablas de la base de datos:

| Tabla | Campos principales | Relaciones |
|---|---|---|
| `user` | `id`, `name`, `email`, `password`, `lat`, `lng`, `online`, `createdAt` | — |
| `chat` | `id`, `type` (`general`/`private`), `isActive`, `createdAt` | — |
| `chat_member` | `id`, `chat_id`, `user_id` | ManyToOne → `chat`, `user` |
| `message` | `id`, `text`, `createdAt`, `chat_id`, `user_id` | ManyToOne → `chat`, `user` |
| `user_block` | `id`, `blocker_id`, `blocked_id`, `createdAt` | ManyToOne → `user` (×2) |
| `user_follow` | `id`, `follower_id`, `followed_id`, `createdAt` | ManyToOne → `user` (×2) |
| `friend_request` | `id`, `senderUser_id`, `receiverUser_id`, `status`, `createdAt`, `respondedAt` | ManyToOne → `user` (×2) |

El campo `status` de `friend_request` puede tomar los valores: `pending`, `accepted`, `rejected` o `cancelled`.  
Los campos `lat` y `lng` de `user` son `decimal(10,8)` y `decimal(11,8)` respectivamente, con precisión suficiente para cálculos de distancia geodésica.

#### Diseño Técnico

- **Arquitectura:** API REST pura (sin renderizado de vistas en servidor). El backend expone únicamente endpoints JSON; el frontend es estático en `public/`.
- **Stack tecnológico:**
  - Backend: **Symfony 7.2 / PHP ≥8.2** — framework MVC usado solo en su capa de controladores y servicios
  - ORM: **Doctrine ORM 3.6** con migraciones gestionadas por `doctrine-migrations-bundle`
  - Base de datos: **MySQL 8.0** en local (XAMPP) / **PostgreSQL** en producción (Railway/Render)
  - Autenticación: capa doble — `ApiKeySubscriber` valida el header `X-API-KEY` en todas las rutas `/api`; `JwtService` (basado en `lcobucci/jwt 4.0`) emite y valida tokens Bearer con duración de 1 hora
  - Frontend: HTML/CSS/JavaScript vanilla en `public/` — sin framework, consume la API con `fetch()`
- **Seguridad:**
  - `CorsSubscriber` gestiona las cabeceras CORS para permitir peticiones desde cualquier origen en desarrollo.
  - `JwtAuthenticator` en `src/Security/` extrae el `user_id` del token y lo inyecta en el contexto de seguridad de Symfony.
  - Las contraseñas se almacenan con hash mediante `PasswordHasherInterface` de Symfony.
- **Cálculo de proximidad:** `HomeController` filtra usuarios usando la fórmula de Haversine aplicada sobre los campos `lat`/`lng` de la entidad `User`, devolviendo solo aquellos en un radio de 5 km.
- **Diseño de la API:** Todos los endpoints siguen el patrón `{data, error}`. Los métodos HTTP, rutas y cuerpos de petición/respuesta están documentados exhaustivamente en las secciones anteriores de este documento.

---

### 3. Implementación (Desarrollo)

El entorno de desarrollo local se configuró con **XAMPP** (Apache + MySQL 8.0), **Composer** como gestor de dependencias PHP, y **Git** para control de versiones.

#### Backend — Symfony

El backend se organiza siguiendo la estructura estándar de Symfony:

| Carpeta | Contenido |
|---|---|
| `src/Entity/` | 7 entidades Doctrine: `User`, `Chat`, `ChatMember`, `Message`, `UserBlock`, `UserFollow`, `FriendRequest` |
| `src/Controller/` | 19 controladores REST: `LoginController`, `LogoutController`, `HomeController`, `UpdateController`, `GeneralController`, `GeneralMessageController`, `PrivateController`, `PrivateChatController`, `MessageController`, `UserController`, `ProfileController`, `FollowController`, `FriendshipController`, `BlockController`, `InviteController`, `ConfigController`, `HealthController`, `DebugController`, `ApiDocController` |
| `src/Repository/` | Repositorios Doctrine con consultas DQL para cada entidad |
| `src/Service/` | `JwtService` — generación y validación de tokens JWT con `lcobucci/jwt` |
| `src/Security/` | `JwtAuthenticator` — autenticador de Symfony basado en token Bearer |
| `src/EventSubscriber/` | `ApiKeySubscriber` (valida `X-API-KEY`), `CorsSubscriber` (cabeceras CORS) |
| `migrations/` | Migraciones de esquema generadas con `doctrine:migrations:diff` |
| `src/DataFixtures/` | `AppFixtures` — 21 usuarios de prueba geolocalizados en Valencia |

#### Frontend

- Interfaz de usuario en `public/index.html` — permite probar todos los endpoints desde el navegador
- Documentación interactiva en `public/docs.html` y `public/endpoints.html`
- Toda la interactividad se implementa con JavaScript vanilla usando `fetch()` contra los endpoints de la propia API

#### Integración Backend–Frontend

La comunicación se verificó en dos niveles:
1. **Postman:** colecciones completas en `postman_collection.json` y `postman_collection_updated.json` con todos los endpoints, variables de entorno y ejemplos de respuesta.
2. **Scripts automatizados PowerShell:** `test_api.ps1`, `test_all_endpoints.ps1` y `test_endpoints_simple.ps1` ejecutan el flujo completo (login → actualizar ubicación → home → mensajes → privados).

---

### 4. Pruebas (Testing) y Control de Calidad

- **Pruebas Unitarias:** Se probó de forma aislada `JwtService`: generación de token con claims `user_id` y `email`, validación de firma, y expiración a 1 hora. También se verificaron los métodos de cálculo de distancia en `HomeController`.
- **Pruebas de Integración:** Cada controlador fue probado end-to-end con peticiones HTTP reales: se comprobó que `ApiKeySubscriber` rechaza peticiones sin cabecera `X-API-KEY`, que `JwtAuthenticator` bloquea tokens expirados, y que Doctrine persiste y recupera correctamente las relaciones entre entidades.
- **Pruebas de Aceptación:** Los 19 controladores y sus rutas fueron validados contra los requisitos funcionales. Los resultados completos están en [TEST_RESULTS.md](TEST_RESULTS.md).
- **Resolución de Errores:** Depuración con el perfilador de Symfony (`/_profiler`) en `APP_ENV=dev` y consulta de logs en `var/log/dev.log`. Para el entorno de producción se habilitó temporalmente `APP_DEBUG=1` en Render y Railway para diagnosticar problemas de conexión a base de datos (documentados en [TROUBLESHOOTING_RENDER.md](TROUBLESHOOTING_RENDER.md) y [RENDER_DEBUG.md](RENDER_DEBUG.md)).

---

### 5. Despliegue (Deployment) y Puesta en Producción

La API está preparada para desplegarse en tres plataformas cloud, cada una con su fichero de configuración propio en el repositorio:

| Plataforma | Fichero de config | Tipo de BD | Notas |
|---|---|---|---|
| **Railway** | `compose.yaml` + `Dockerfile` | PostgreSQL | Recomendado. Deploy automático desde GitHub |
| **Render** | `render.yaml` | PostgreSQL | Blueprint autodeploy. Ver [DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md) |
| **Fly.io** | `fly.toml` | PostgreSQL | Alternativa con más control sobre la instancia |

- **Preparación del entorno de producción:** El `Dockerfile` instala las dependencias de sistema necesarias (`ext-pdo_pgsql`), ejecuta `composer install --no-dev` y lanza el servidor PHP. El script `docker-entrypoint.sh` aplica las migraciones (`doctrine:migrations:migrate`) automáticamente en cada despliegue.
- **Variables de entorno necesarias en producción:**
  ```
  APP_ENV=prod
  APP_DEBUG=0
  APP_SECRET=<32 chars aleatorios>
  APP_API_KEY=test-api-key
  DATABASE_URL=postgresql://user:pass@host:5432/dbname
  JWT_SECRET=<clave secreta para firma de tokens>
  ```
- **Dominio y SSL:** Railway y Render asignan automáticamente un subdominio `*.railway.app` / `*.onrender.com` con certificado SSL/TLS gestionado por Let's Encrypt, sin configuración adicional.
- **Datos iniciales:** Tras el primer despliegue, se ejecuta `doctrine:fixtures:load` para cargar los 21 usuarios de prueba geolocalizados en Valencia (definidos en `AppFixtures` y disponibles también en `valencia_users.sql`).
- **Documentación de despliegue detallada:** [DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md) · [RAILWAY_SIMPLE.md](RAILWAY_SIMPLE.md) · [RENDER_DEPLOYMENT.md](RENDER_DEPLOYMENT.md) · [PASOS_RENDER.md](PASOS_RENDER.md)
