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

- **Definición del Alcance:** Identificar las necesidades del cliente y establecer los objetivos y límites del proyecto. Un proyecto sin límites no sería abordable porque no terminaría nunca. Hay que modularlo para que sea factible.
- **Análisis de Viabilidad:** Técnica y económica. La parte técnica indica qué es posible hacer con las herramientas actuales; la económica incluye costes de servidores, dominios y licencias, así como el esfuerzo humano traducido a precio/hora.
- **Planificación Temporal:** Creación de diagramas de Gantt, hitos y cronogramas.
- **Asignación de Recursos y Roles:** Aunque sea un proyecto pequeño, puede ser necesario repartir las tareas de manera clara y temporizada entre los miembros del equipo.
- **Elaboración del Documento de Especificación de Requisitos:** Recoge todo lo que la aplicación debe hacer y lo que se espera de ella. De este documento suele derivarse el presupuesto del proyecto.

En este proyecto, el alcance quedó definido como una **API REST de chat geolocalizado** con autenticación JWT, gestión de usuarios, chats privados y generales, seguimientos y bloqueos — descartando explícitamente funcionalidades en tiempo real (WebSockets) para mantener el scope factible.

---

### 2. Análisis y Diseño

El análisis afecta tanto a los datos que va a manejar la aplicación como a la tecnología a utilizar. Es fundamental para no estar "dando palos de ciego" y evitar cambios costosos en tiempo y dinero.

#### Diseño de la Base de Datos

Modelo Entidad-Relación (ER) y modelo relacional. Definición de tablas, claves, relaciones y normalización.

| Entidad | Descripción |
|---|---|
| `user` | Usuarios con geolocalización (lat/lon) |
| `chat` | Salas de chat (tipo: general / privado) |
| `chat_member` | Relación usuario-sala |
| `message` | Mensajes con timestamp y contenido |
| `user_block` | Relaciones de bloqueo entre usuarios |
| `user_follow` | Relaciones de seguimiento |
| `friend_request` | Solicitudes de amistad con estado |

#### Diseño Técnico

- **Arquitectura:** API REST (sin vistas servidor) con separación total Backend / Frontend.
- **Stack tecnológico:**
  - Backend: **Symfony 7.2 / PHP 8.2** con Doctrine ORM 3.6
  - Base de datos: **MySQL 8.0** (local) / **PostgreSQL** (producción)
  - Autenticación: **API Key** (cabecera `X-API-KEY`) + **JWT** (`lcobucci/jwt 4.0`)
  - Frontend: HTML/CSS/JavaScript vanilla consumiendo la API
- **Seguridad:** Doble capa con API Key global y JWT por usuario. CORS gestionado mediante `CorsSubscriber`.
- **Diseño de la API:** Endpoints RESTful documentados en este mismo documento, con respuestas en formato JSON estandarizado `{data, error}`.

---

### 3. Implementación (Desarrollo)

Tras el diseño, se configuró el entorno de desarrollo local con **XAMPP** (Apache + MySQL), **Composer** como gestor de dependencias PHP, y **Git** para control de versiones.

#### Backend (Symfony)

- Modelos (Entidades Doctrine): `src/Entity/`
- Controladores con lógica de negocio: `src/Controller/`
- Repositorios con consultas DQL/nativas: `src/Repository/`
- Servicios reutilizables (JWT, utilidades): `src/Service/`
- Autenticación y autorización: `src/Security/` + `src/EventSubscriber/`
- Migraciones de base de datos: `migrations/`

#### Frontend

- Maquetación con HTML/CSS y Bootstrap: `public/*.html`
- Interactividad con JavaScript vanilla: consumo de la API REST
- Interfaz de pruebas integrada: `public/index.html`, `public/docs.html`

#### Integración Backend–Frontend

La comunicación se verificó mediante **Postman** (colecciones incluidas en `postman_collection.json`) y los scripts de prueba automatizados disponibles en `test_api.ps1` y `test_all_endpoints.ps1`.

---

### 4. Pruebas (Testing) y Control de Calidad

- **Pruebas Unitarias:** Validación aislada de servicios como `JwtService` y lógica de negocio de los controladores.
- **Pruebas de Integración:** Verificación de la comunicación entre controladores, repositorios y base de datos mediante solicitudes HTTP reales.
- **Pruebas de Aceptación:** Los endpoints fueron probados contra los requisitos funcionales definidos (ver `TEST_RESULTS.md`).
- **Resolución de Errores:** Depuración con el perfilador de Symfony (`/_profiler`) en entorno `dev` y logs en `var/log/`.

Los resultados de las pruebas están documentados en [TEST_RESULTS.md](TEST_RESULTS.md).

---

### 5. Despliegue (Deployment) y Puesta en Producción

La puesta en producción traslada el proyecto local a un entorno accesible para todos los usuarios.

- **Preparación del Entorno:** Configuración de servidor web (Apache/Nginx), base de datos PostgreSQL en la nube y variables de entorno de producción (`APP_ENV=prod`, `APP_DEBUG=0`).
- **Plataformas soportadas:**
  - **Railway** (recomendado): despliegue automático desde GitHub con `railway.toml`
  - **Render**: despliegue mediante `render.yaml` con Blueprint
  - **Fly.io**: configuración en `fly.toml`
- **Dominio y SSL:** Las plataformas Railway/Render proporcionan subdominios con certificado SSL/TLS automático mediante Let's Encrypt.
- **Documentación de Despliegue:** Instrucciones detalladas disponibles en [DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md), [RAILWAY_SIMPLE.md](RAILWAY_SIMPLE.md) y [RENDER_DEPLOYMENT.md](RENDER_DEPLOYMENT.md).
