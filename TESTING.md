# 🧪 Guía de Testing - Antojes API

Ejemplos prácticos para probar todos los endpoints usando curl o Postman.

---

## 🔑 Variables de Entorno para Tests

```bash
# Define estas variables para reutilizar
API_KEY="test-api-key"
BASE_URL="http://localhost:8000/api"

# Tokens (se obtienen después de login)
TOKEN=""
TOKEN_USER1=""
TOKEN_USER2=""

# IDs de prueba
CHAT_ID=2  # Chat privado
USER_ID=2  # Otro usuario
```

---

## 🧑‍💻 1. Gestión de Usuarios

### ✅ Crear usuario (público)
```bash
curl -X POST $BASE_URL/usuarios \
  -H "X-API-KEY: $API_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "testuser@example.com",
    "password": "TestPassword123"
  }'
```
**Esperado:** Status 201 - User created

---

### ✅ Listar usuarios (privado)
```bash
curl -X GET $BASE_URL/usuarios \
  -H "X-API-KEY: $API_KEY" \
  -H "Authorization: Bearer $TOKEN"
```
**Esperado:** Status 200 - Lista de usuarios

---

### ✅ Obtener usuario por ID
```bash
curl -X GET $BASE_URL/usuarios/1 \
  -H "X-API-KEY: $API_KEY" \
  -H "Authorization: Bearer $TOKEN"
```
**Esperado:** Status 200 - Datos del usuario

---

### ✅ Actualizar usuario (solo propio)
```bash
curl -X PUT $BASE_URL/usuarios/1 \
  -H "X-API-KEY: $API_KEY" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Updated Name",
    "email": "newemail@example.com"
  }'
```
**Esperado:** Status 200 - User updated

---

### ❌ Actualizar otro usuario (debe fallar)
```bash
curl -X PUT $BASE_URL/usuarios/999 \
  -H "X-API-KEY: $API_KEY" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"name": "Hacker"}'
```
**Esperado:** Status 403 - Forbidden

---

### ✅ Eliminar usuario (solo propio)
```bash
curl -X DELETE $BASE_URL/usuarios/1 \
  -H "X-API-KEY: $API_KEY" \
  -H "Authorization: Bearer $TOKEN"
```
**Esperado:** Status 200 - User deleted

---

## 🔐 2. Autenticación

### ✅ Login (público)
```bash
curl -X POST $BASE_URL/login \
  -H "X-API-KEY: $API_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user1@example.com",
    "password": "password"
  }'
```
**Respuesta esperada:**
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

---

### ❌ Login con credenciales incorrectas
```bash
curl -X POST $BASE_URL/login \
  -H "X-API-KEY: $API_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user1@example.com",
    "password": "wrongpassword"
  }'
```
**Esperado:** Status 401 - Invalid credentials

---

### ✅ Logout
```bash
curl -X POST $BASE_URL/logout \
  -H "X-API-KEY: $API_KEY" \
  -H "Authorization: Bearer $TOKEN"
```
**Esperado:** Status 200 - Logged out

---

## 🏠 3. Home y Perfil

### ✅ Obtener Home (usuario + cercanos)
```bash
curl -X GET $BASE_URL/home \
  -H "X-API-KEY: $API_KEY" \
  -H "Authorization: Bearer $TOKEN"
```
**Respuesta esperada:**
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

**Notas:**
- Solo muestra usuarios a menos de 5 km
- Excluye usuarios bloqueados
- El usuario lejano (user4) NO aparecerá

---

### ✅ Obtener Perfil
```bash
curl -X GET $BASE_URL/perfil \
  -H "X-API-KEY: $API_KEY" \
  -H "Authorization: Bearer $TOKEN"
```

---

### ✅ Actualizar ubicación
```bash
curl -X POST $BASE_URL/actualizar \
  -H "X-API-KEY: $API_KEY" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "lat": "40.7500",
    "lng": "-74.0100"
  }'
```
**Esperado:** Status 200 - Location updated

---

## 💬 4. Chat General

### ✅ Obtener chat general
```bash
curl -X GET $BASE_URL/general \
  -H "X-API-KEY: $API_KEY" \
  -H "Authorization: Bearer $TOKEN"
```
**Esperado:** Chat con ID=1 y últimos 50 mensajes

---

### ✅ Enviar mensaje al chat general
```bash
curl -X POST "$BASE_URL/mensaje?chat_id=1" \
  -H "X-API-KEY: $API_KEY" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "text": "¡Hola a todos desde la API!"
  }'
```
**Esperado:** Status 200 - Message sent

---

### ✅ Obtener mensajes del chat general
```bash
curl -X GET "$BASE_URL/mensaje?chat_id=1" \
  -H "X-API-KEY: $API_KEY" \
  -H "Authorization: Bearer $TOKEN"
```
**Esperado:** Array de mensajes ordenados cronológicamente

---

### ❌ Enviar mensaje a chat privado sin ser miembro
```bash
curl -X POST "$BASE_URL/mensaje?chat_id=999" \
  -H "X-API-KEY: $API_KEY" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"text": "Hack attempt"}'
```
**Esperado:** Status 403 - Not a member of this chat

---

## 👥 5. Chats Privados

### ✅ Crear/obtener chat privado (invitación)
```bash
curl -X POST $BASE_URL/invitar \
  -H "X-API-KEY: $API_KEY" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"user_id": 2}'
```
**Respuesta (si es nuevo, status 201):**
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

**Respuesta (si ya existe, status 200):**
```json
{
  "message": "Chat already exists",
  "chat": {
    "id": 2,
    "type": "private",
    "is_active": true
  }
}
```

---

### ✅ Listar chats privados activos
```bash
curl -X GET $BASE_URL/privado \
  -H "X-API-KEY: $API_KEY" \
  -H "Authorization: Bearer $TOKEN"
```

---

### ✅ Cambiar chat activo
```bash
curl -X POST $BASE_URL/privado/cambiar/chat \
  -H "X-API-KEY: $API_KEY" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"chat_id": 2}'
```

---

### ✅ Enviar mensaje a chat privado
```bash
curl -X POST "$BASE_URL/mensaje?chat_id=2" \
  -H "X-API-KEY: $API_KEY" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"text": "Mensaje privado"}'
```

---

### ✅ Salir del chat privado
```bash
curl -X POST $BASE_URL/privado/salir \
  -H "X-API-KEY: $API_KEY" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"chat_id": 2}'
```
**Efecto:**
- Marca `leftAt` en ChatMember
- Si ambos usuarios han salido, marca chat como `is_active = false`

---

## 🚫 6. Bloqueos

### ✅ Bloquear usuario
```bash
curl -X POST $BASE_URL/bloquear \
  -H "X-API-KEY: $API_KEY" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"user_id": 4}'
```
**Esperado:** Status 201 - User blocked

---

### ❌ Intentar bloquear a uno mismo
```bash
curl -X POST $BASE_URL/bloquear \
  -H "X-API-KEY: $API_KEY" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"user_id": 1}'  # Tu propio ID
```
**Esperado:** Status 400 - Cannot block yourself

---

### ✅ Desbloquear usuario
```bash
curl -X DELETE $BASE_URL/bloquear/4 \
  -H "X-API-KEY: $API_KEY" \
  -H "Authorization: Bearer $TOKEN"
```

---

### ❌ Invitar a usuario bloqueado (debe fallar)
```bash
# 1. Bloqueamos a user4
curl -X POST $BASE_URL/bloquear \
  -H "X-API-KEY: $API_KEY" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"user_id": 4}'

# 2. Intentamos invitar (debería ser rechazado en validación)
curl -X POST $BASE_URL/invitar \
  -H "X-API-KEY: $API_KEY" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"user_id": 4}'
```

---

## 👥 7. Seguimiento

### ✅ Seguir usuario
```bash
curl -X POST $BASE_URL/seguir \
  -H "X-API-KEY: $API_KEY" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"user_id": 3}'
```

---

### ✅ Dejar de seguir
```bash
curl -X DELETE $BASE_URL/seguir/3 \
  -H "X-API-KEY: $API_KEY" \
  -H "Authorization: Bearer $TOKEN"
```

---

## 🤝 8. Solicitudes de Amistad

### ✅ Solicitar amistad
```bash
curl -X POST $BASE_URL/amistad/solicitar \
  -H "X-API-KEY: $API_KEY" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"user_id": 4}'
```
**Respuesta:**
```json
{
  "message": "Friend request sent",
  "request_id": 5
}
```

---

### ❌ Solicitar amistad a uno mismo
```bash
curl -X POST $BASE_URL/amistad/solicitar \
  -H "X-API-KEY: $API_KEY" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"user_id": 1}'  # Tu propio ID
```
**Esperado:** Status 400

---

### ✅ Aceptar solicitud de amistad
```bash
# Primero, user2 recibe solicitud de user1
# TOKEN debe ser de user2
curl -X POST $BASE_URL/amistad/aceptar \
  -H "X-API-KEY: $API_KEY" \
  -H "Authorization: Bearer $TOKEN_USER2" \
  -H "Content-Type: application/json" \
  -d '{"request_id": 1}'
```

---

### ✅ Rechazar solicitud de amistad
```bash
curl -X POST $BASE_URL/amistad/rechazar \
  -H "X-API-KEY: $API_KEY" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"request_id": 2}'
```

---

### ✅ Listar amigos (aceptados)
```bash
curl -X GET $BASE_URL/amistad \
  -H "X-API-KEY: $API_KEY" \
  -H "Authorization: Bearer $TOKEN"
```

---

## ⚠️ 9. Casos de Error

### ❌ Sin header X-API-KEY
```bash
curl -X GET $BASE_URL/home \
  -H "Authorization: Bearer $TOKEN"
```
**Esperado:** Status 401 - Invalid API Key

---

### ❌ API-KEY incorrecta
```bash
curl -X GET $BASE_URL/home \
  -H "X-API-KEY: wrong-key" \
  -H "Authorization: Bearer $TOKEN"
```
**Esperado:** Status 401 - Invalid API Key

---

### ❌ Sin token JWT (endpoint privado)
```bash
curl -X GET $BASE_URL/home \
  -H "X-API-KEY: $API_KEY"
```
**Esperado:** Status 401 - Token required

---

### ❌ Token JWT inválido
```bash
curl -X GET $BASE_URL/home \
  -H "X-API-KEY: $API_KEY" \
  -H "Authorization: Bearer invalid.token.here"
```
**Esperado:** Status 401 - Invalid token

---

### ❌ Endpoint no encontrado
```bash
curl -X GET $BASE_URL/nonexistent \
  -H "X-API-KEY: $API_KEY" \
  -H "Authorization: Bearer $TOKEN"
```
**Esperado:** Status 404 - Not Found

---

## 🔄 10. Flujo Completo (Paso a Paso)

### 1. Crear usuario
```bash
curl -X POST http://localhost:8000/api/usuarios \
  -H "X-API-KEY: test-api-key" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Mi Usuario",
    "email": "miusuario@example.com",
    "password": "MiPassword123"
  }'
```

### 2. Login
```bash
TOKEN=$(curl -s -X POST http://localhost:8000/api/login \
  -H "X-API-KEY: test-api-key" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "miusuario@example.com",
    "password": "MiPassword123"
  }' | jq -r '.token')

echo "Token: $TOKEN"
```

### 3. Obtener home
```bash
curl -X GET http://localhost:8000/api/home \
  -H "X-API-KEY: test-api-key" \
  -H "Authorization: Bearer $TOKEN"
```

### 4. Ver chat general
```bash
curl -X GET http://localhost:8000/api/general \
  -H "X-API-KEY: test-api-key" \
  -H "Authorization: Bearer $TOKEN"
```

### 5. Enviar mensaje
```bash
curl -X POST "http://localhost:8000/api/mensaje?chat_id=1" \
  -H "X-API-KEY: test-api-key" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"text": "¡Mi primer mensaje!"}'
```

### 6. Invitar a chat privado
```bash
curl -X POST http://localhost:8000/api/invitar \
  -H "X-API-KEY: test-api-key" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"user_id": 2}'
```

### 7. Enviar mensaje privado
```bash
curl -X POST "http://localhost:8000/api/mensaje?chat_id=2" \
  -H "X-API-KEY: test-api-key" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"text": "¡Hola en privado!"}'
```

### 8. Salir del chat
```bash
curl -X POST http://localhost:8000/api/privado/salir \
  -H "X-API-KEY: test-api-key" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"chat_id": 2}'
```

---

## 🛠️ Herramientas Recomendadas

- **curl** - Línea de comandos (ejemplos en esta guía)
- **Postman** - GUI interactivo
- **Insomnia** - Alternativa a Postman
- **Thunder Client** - Extensión de VS Code

---

## 📝 Notas

- Todos los tokens JWT duran **1 hora**
- La distancia en `/api/home` se calcula con **Haversine**
- Los bloqueos se aplican **bilateralmente** a nivel lógico
- Los chats privados se **reutilizan** automáticamente
- Las fixtures incluyen datos de prueba listos para usar

---

**Última actualización:** 29/01/2026
