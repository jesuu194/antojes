# 🧪 Chat Geolocalizado - Guía de Prueba con Postman

## 📋 Configuración Inicial

### URL Base
```
http://127.0.0.1:8000/api
```

### Headers Globales (todos los endpoints)
```
X-API-KEY: test-api-key
Content-Type: application/json
```

### Variables de Environment (Postman)
Crear un environment llamado "Chat Geolocalizado" con:
```
- base_url: http://127.0.0.1:8000/api
- api_key: test-api-key
- token: (se genera después del login)
```

---

## 🔑 1. AUTENTICACIÓN

### 1.1 Login
**POST** `{{base_url}}/login`

**Headers:**
```
X-API-KEY: test-api-key
Content-Type: application/json
```

**Body (JSON):**
```json
{
  "email": "user1@example.com",
  "password": "password"
}
```

**Response (200):**
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
  "user": {
    "id": 5,
    "name": "User One",
    "email": "user1@example.com"
  }
}
```

💡 **Guarda el token en la variable `{{token}}` del environment**

---

### 1.2 Crear Cuenta
**POST** `{{base_url}}/usuarios`

**Headers:**
```
X-API-KEY: test-api-key
Content-Type: application/json
```

**Body (JSON):**
```json
{
  "name": "New User",
  "email": "newuser@example.com",
  "password": "password123"
}
```

**Response (201):**
```json
{
  "id": 10,
  "name": "New User",
  "email": "newuser@example.com"
}
```

---

### 1.3 Logout
**POST** `{{base_url}}/logout`

**Headers:**
```
X-API-KEY: test-api-key
Authorization: Bearer {{token}}
```

**Response (200):**
```json
{
  "message": "Logged out"
}
```

---

## 📍 2. GEOLOCALIZACIÓN Y HOME

### 2.1 Home (Usuarios cercanos)
**GET** `{{base_url}}/home`

**Headers:**
```
X-API-KEY: test-api-key
Authorization: Bearer {{token}}
```

**Response (200):**
```json
{
  "user": {
    "id": 5,
    "name": "User One",
    "email": "user1@example.com",
    "lat": 40.7128,
    "lng": -74.0060,
    "online": true
  },
  "nearby_users": [
    {
      "id": 6,
      "name": "User Two",
      "email": "user2@example.com",
      "distance": 0.02
    }
  ]
}
```

---

### 2.2 Actualizar Ubicación
**POST** `{{base_url}}/actualizar`

**Headers:**
```
X-API-KEY: test-api-key
Authorization: Bearer {{token}}
Content-Type: application/json
```

**Body (JSON):**
```json
{
  "lat": 40.7180,
  "lng": -74.0080
}
```

**Response (200):**
```json
{
  "message": "Location updated"
}
```

---

## 💬 3. CHATS

### 3.1 Chat General
**GET** `{{base_url}}/general`

**Headers:**
```
X-API-KEY: test-api-key
Authorization: Bearer {{token}}
```

**Response (200):**
```json
{
  "chat_id": 1,
  "chat_type": "general",
  "messages": [
    {
      "id": 1,
      "user_id": 5,
      "user_name": "User One",
      "text": "Hola a todos!",
      "created_at": "2026-01-29 10:00:00"
    }
  ]
}
```

---

### 3.2 Enviar Mensaje al Chat General
**POST** `{{base_url}}/mensaje?chat_id=1`

**Headers:**
```
X-API-KEY: test-api-key
Authorization: Bearer {{token}}
Content-Type: application/json
```

**Body (JSON):**
```json
{
  "text": "¡Hola a todos desde Postman!"
}
```

**Response (201):**
```json
{
  "id": 5,
  "chat_id": 1,
  "user_id": 5,
  "text": "¡Hola a todos desde Postman!",
  "created_at": "2026-01-29 10:15:30"
}
```

---

### 3.3 Crear Chat Privado (Invitar Usuario)
**POST** `{{base_url}}/invitar`

**Headers:**
```
X-API-KEY: test-api-key
Authorization: Bearer {{token}}
Content-Type: application/json
```

**Body (JSON):**
```json
{
  "user_id": 6
}
```

**Response (201):**
```json
{
  "chat_id": 3,
  "message": "Chat created/activated"
}
```

---

### 3.4 Listar Chats Privados
**GET** `{{base_url}}/privado`

**Headers:**
```
X-API-KEY: test-api-key
Authorization: Bearer {{token}}
```

**Response (200):**
```json
{
  "chats": [
    {
      "id": 3,
      "type": "private",
      "other_user": {
        "id": 6,
        "name": "User Two",
        "email": "user2@example.com"
      }
    }
  ]
}
```

---

### 3.5 Enviar Mensaje Privado
**POST** `{{base_url}}/mensaje?chat_id=3`

**Headers:**
```
X-API-KEY: test-api-key
Authorization: Bearer {{token}}
Content-Type: application/json
```

**Body (JSON):**
```json
{
  "text": "Hola, ¿cómo estás?"
}
```

**Response (201):**
```json
{
  "id": 6,
  "chat_id": 3,
  "user_id": 5,
  "text": "Hola, ¿cómo estás?",
  "created_at": "2026-01-29 10:20:00"
}
```

---

### 3.6 Salir de Chat Privado
**POST** `{{base_url}}/privado/salir`

**Headers:**
```
X-API-KEY: test-api-key
Authorization: Bearer {{token}}
Content-Type: application/json
```

**Body (JSON):**
```json
{
  "chat_id": 3
}
```

**Response (200):**
```json
{
  "message": "Exited chat"
}
```

---

## 🚫 4. BLOQUEOS

### 4.1 Bloquear Usuario
**POST** `{{base_url}}/bloquear`

**Headers:**
```
X-API-KEY: test-api-key
Authorization: Bearer {{token}}
Content-Type: application/json
```

**Body (JSON):**
```json
{
  "user_id": 7
}
```

**Response (201):**
```json
{
  "message": "User blocked"
}
```

---

### 4.2 Obtener Usuarios Bloqueados
**GET** `{{base_url}}/bloqueados`

**Headers:**
```
X-API-KEY: test-api-key
Authorization: Bearer {{token}}
```

**Response (200):**
```json
{
  "blocked_users": [
    {
      "id": 7,
      "name": "User Three",
      "email": "user3@example.com"
    }
  ]
}
```

---

### 4.3 Desbloquear Usuario
**DELETE** `{{base_url}}/bloquear/7`

**Headers:**
```
X-API-KEY: test-api-key
Authorization: Bearer {{token}}
```

**Response (200):**
```json
{
  "message": "User unblocked"
}
```

---

## 👤 5. SEGUIMIENTOS

### 5.1 Seguir Usuario
**POST** `{{base_url}}/seguir`

**Headers:**
```
X-API-KEY: test-api-key
Authorization: Bearer {{token}}
Content-Type: application/json
```

**Body (JSON):**
```json
{
  "user_id": 6
}
```

**Response (201):**
```json
{
  "message": "User followed"
}
```

---

### 5.2 Obtener Usuarios Seguidos
**GET** `{{base_url}}/seguidos`

**Headers:**
```
X-API-KEY: test-api-key
Authorization: Bearer {{token}}
```

**Response (200):**
```json
{
  "followed_users": [
    {
      "id": 6,
      "name": "User Two",
      "email": "user2@example.com"
    }
  ]
}
```

---

### 5.3 Dejar de Seguir
**DELETE** `{{base_url}}/seguir/6`

**Headers:**
```
X-API-KEY: test-api-key
Authorization: Bearer {{token}}
```

**Response (200):**
```json
{
  "message": "User unfollowed"
}
```

---

## 🤝 6. AMISTADES

### 6.1 Enviar Solicitud de Amistad
**POST** `{{base_url}}/amistad/solicitar`

**Headers:**
```
X-API-KEY: test-api-key
Authorization: Bearer {{token}}
Content-Type: application/json
```

**Body (JSON):**
```json
{
  "user_id": 8
}
```

**Response (201):**
```json
{
  "message": "Friendship request sent"
}
```

---

### 6.2 Listar Amigos
**GET** `{{base_url}}/amistad`

**Headers:**
```
X-API-KEY: test-api-key
Authorization: Bearer {{token}}
```

**Response (200):**
```json
{
  "friends": [
    {
      "id": 6,
      "name": "User Two",
      "email": "user2@example.com"
    }
  ]
}
```

---

### 6.3 Listar Solicitudes Pendientes
**GET** `{{base_url}}/amistad/pendientes`

**Headers:**
```
X-API-KEY: test-api-key
Authorization: Bearer {{token}}
```

**Response (200):**
```json
{
  "pending_requests": [
    {
      "id": 1,
      "sender_id": 9,
      "sender_name": "User Four",
      "sender_email": "user4@example.com"
    }
  ]
}
```

---

### 6.4 Aceptar Solicitud de Amistad
**POST** `{{base_url}}/amistad/aceptar`

**Headers:**
```
X-API-KEY: test-api-key
Authorization: Bearer {{token}}
Content-Type: application/json
```

**Body (JSON):**
```json
{
  "friendship_request_id": 1
}
```

**Response (200):**
```json
{
  "message": "Friendship accepted"
}
```

---

### 6.5 Rechazar Solicitud de Amistad
**POST** `{{base_url}}/amistad/rechazar`

**Headers:**
```
X-API-KEY: test-api-key
Authorization: Bearer {{token}}
Content-Type: application/json
```

**Body (JSON):**
```json
{
  "friendship_request_id": 1
}
```

**Response (200):**
```json
{
  "message": "Friendship rejected"
}
```

---

## 👥 7. GESTIÓN DE USUARIOS

### 7.1 Listar Todos los Usuarios
**GET** `{{base_url}}/usuarios`

**Headers:**
```
X-API-KEY: test-api-key
Authorization: Bearer {{token}}
```

**Response (200):**
```json
{
  "users": [
    {
      "id": 5,
      "name": "User One",
      "email": "user1@example.com"
    }
  ]
}
```

---

### 7.2 Obtener Perfil
**GET** `{{base_url}}/perfil`

**Headers:**
```
X-API-KEY: test-api-key
Authorization: Bearer {{token}}
```

**Response (200):**
```json
{
  "id": 5,
  "name": "User One",
  "email": "user1@example.com",
  "lat": 40.7128,
  "lng": -74.0060,
  "online": true,
  "created_at": "2026-01-29 09:00:00"
}
```

---

### 7.3 Actualizar Perfil
**PUT** `{{base_url}}/usuarios/5`

**Headers:**
```
X-API-KEY: test-api-key
Authorization: Bearer {{token}}
Content-Type: application/json
```

**Body (JSON):**
```json
{
  "name": "User One Updated",
  "email": "user1updated@example.com"
}
```

**Response (200):**
```json
{
  "id": 5,
  "name": "User One Updated",
  "email": "user1updated@example.com"
}
```

---

### 7.4 Eliminar Usuario
**DELETE** `{{base_url}}/usuarios/10`

**Headers:**
```
X-API-KEY: test-api-key
Authorization: Bearer {{token}}
```

**Response (200):**
```json
{
  "message": "User deleted"
}
```

---

## ✅ CHECKLIST DE PRUEBA

Usa este checklist para asegurar que todo funciona:

- [ ] Login exitoso (obtener token)
- [ ] Crear cuenta nueva
- [ ] Actualizar ubicación
- [ ] Ver usuarios cercanos (home)
- [ ] Enviar mensaje al chat general
- [ ] Crear chat privado
- [ ] Enviar mensaje privado
- [ ] Bloquear usuario
- [ ] Ver usuarios bloqueados
- [ ] Desbloquear usuario
- [ ] Seguir usuario
- [ ] Ver usuarios seguidos
- [ ] Enviar solicitud de amistad
- [ ] Ver solicitudes pendientes
- [ ] Aceptar solicitud de amistad
- [ ] Listar amigos
- [ ] Salir de chat privado
- [ ] Logout

---

## 🔍 CÓDIGOS DE RESPUESTA

| Código | Significado |
|--------|-------------|
| 200 | OK - Solicitud exitosa |
| 201 | Created - Recurso creado |
| 400 | Bad Request - Datos inválidos |
| 401 | Unauthorized - API Key o Token inválido |
| 404 | Not Found - Recurso no encontrado |
| 500 | Internal Server Error |

---

## 📝 NOTAS IMPORTANTES

1. **API Key**: Todos los endpoints requieren `X-API-KEY: test-api-key`
2. **Token**: Se obtiene del login y debe incluirse como `Authorization: Bearer {token}`
3. **Usuarios de prueba**:
   - user1@example.com / password
   - user2@example.com / password
   - user3@example.com / password
   - user4@example.com / password
4. **Chat General**: ID = 1 (siempre)
5. **Geolocalización**: Usa Haversine, radio 5km

---

**¡Listo para que tu profesor pruebe todo!** 🚀
