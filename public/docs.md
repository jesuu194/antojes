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