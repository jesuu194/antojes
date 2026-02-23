# Guía para Probar con Postman

## 📥 Importar la Colección

1. Abre Postman
2. Click en **Import**
3. Selecciona el archivo: `postman_collection_updated.json`
4. La colección se importará con todas las variables configuradas

## 🔑 Variables Configuradas

La colección incluye estas variables (ya configuradas):

- `baseUrl`: `http://localhost:8000/api`
- `apiKey`: `test-api-key`
- `token`: (se guarda automáticamente al hacer login)

## 🚀 Cómo Usar

### 1. Login Primero
1. Ve a **1. Autenticación > Login**
2. Click en **Send**
3. El token se guardará automáticamente en las variables de colección
4. Verás el token en la respuesta

### 2. Probar Otros Endpoints
Después del login, puedes probar cualquier endpoint:

#### Información General
- **Home**: Ver usuarios cercanos
- **Chat General**: Ver mensajes del chat general
- **Mi Perfil**: Ver tu perfil de usuario
- **Actualizar Ubicación**: Cambiar tu posición GPS

#### Usuarios (CRUD)
- **Crear Usuario**: No requiere login, solo API Key
- **Listar Usuarios**: Ver todos los usuarios
- **Ver Usuario por ID**: Ver detalles de un usuario específico
- **Eliminar Usuario**: Eliminar un usuario por ID

#### Mensajes y Chats
- **Enviar Mensaje**: Enviar mensaje a un chat (chat_id=1 es el general)
- **Ver Chats Privados**: Listar tus chats privados
- **Cambiar Chat Privado**: Cambiar al chat especificado
- **Salir de Chat Privado**: Abandonar un chat
- **Invitar a Chat Privado**: Crear chat privado con otro usuario

#### Amistades
- **Ver Mis Amistades**: Lista de amigos aceptados
- **Solicitudes Pendientes**: Solicitudes de amistad sin responder
- **Enviar Solicitud**: Enviar solicitud de amistad a otro usuario
- **Aceptar Solicitud**: Aceptar una solicitud (necesitas el request_id)
- **Rechazar Solicitud**: Rechazar una solicitud

#### Seguir/Bloquear
- **Seguir Usuario**: Comenzar a seguir a un usuario
- **Dejar de Seguir**: Dejar de seguir (necesitas el follow_id)
- **Bloquear Usuario**: Bloquear a un usuario
- **Desbloquear Usuario**: Desbloquear (necesitas el block_id)

## 📝 Datos de Prueba

**Usuarios disponibles** (IDs del 1 al 21):
- Email: `test@example.com` - Password: `password123` (ID: 21)
- Email: `maria.garcia@valencia.com` - Password: `password123` (ID: 1)
- Email: `carlos.martinez@valencia.com` - Password: `password123` (ID: 2)
- ... y más usuarios de Valencia

**Chats**:
- Chat ID 1 = Chat General (pública)
- Chat ID 2+ = Chats privados

## ⚠️ Notas Importantes

1. **Siempre haz LOGIN primero** - La mayoría de endpoints requieren autenticación
2. **El token se guarda automáticamente** - Después del login, se usa en todos los requests
3. **API Key siempre necesaria** - Todos los endpoints requieren `X-API-KEY`
4. **IDs válidos**: Usuarios del 1 al 21
5. **Crear Usuario** es el único endpoint público (no requiere token)

## 🔄 Flujo de Prueba Sugerido

### Flujo Completo
```
1. Login (test@example.com)
2. Home (ver usuarios cercanos)
3. Chat General (ver mensajes)
4. Actualizar Ubicación (cambiar GPS)
5. Enviar Mensaje (chat_id=1)
6. Seguir Usuario (user_id=2)
7. Enviar Solicitud Amistad (user_id=3)
8. Invitar a Chat Privado (user_id=2)
9. Ver Chats Privados
10. Ver Mi Perfil
```

### Para Probar CRUD
```
1. Login (test@example.com)
2. Crear Usuario (nuevo usuario)
3. Listar Usuarios (ver todos)
4. Ver Usuario por ID (ID=22, el recién creado)
5. Eliminar Usuario (ID=22)
```

### Para Probar Amistades
```
1. Login con usuario 1
2. Enviar Solicitud (a usuario 2)
3. Logout
4. Login con usuario 2
5. Ver Solicitudes Pendientes
6. Aceptar Solicitud
7. Ver Mis Amistades
```

## 🐛 Solución de Problemas

**Error 401 - Unauthorized**
- Asegúrate de haber hecho login
- Verifica que la variable `token` esté llena
- Verifica que el `apiKey` sea correcto

**Error 404 - Not Found**
- Verifica la URL base: `http://localhost:8000/api`
- Asegúrate de que el servidor PHP esté corriendo

**Error 500 - Internal Server Error**
- Revisa los logs del servidor PHP
- Verifica que la base de datos esté configurada

## ✅ Endpoints Verificados

Todos estos endpoints están funcionando:

✓ POST /api/login
✓ POST /api/logout
✓ GET /api/home
✓ GET /api/general
✓ GET /api/perfil
✓ POST /api/actualizar
✓ GET /api/health
✓ POST /api/usuarios (público)
✓ GET /api/usuarios
✓ GET /api/usuarios/{id}
✓ DELETE /api/usuarios/{id}
✓ POST /api/mensaje
✓ GET /api/privado
✓ POST /api/privado/cambiar/chat
✓ POST /api/privado/salir
✓ POST /api/invitar
✓ GET /api/amistad
✓ GET /api/amistad/pendientes
✓ POST /api/amistad/solicitar
✓ POST /api/amistad/aceptar
✓ POST /api/amistad/rechazar
✓ POST /api/seguir
✓ DELETE /api/seguir/{id}
✓ POST /api/bloquear
✓ DELETE /api/bloquear/{id}
