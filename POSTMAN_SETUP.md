# 📮 Importar Colección en Postman

## 🚀 Pasos para Importar

### 1️⃣ Abrir Postman

Descarga [Postman](https://www.postman.com/downloads/) si aún no lo tienes.

### 2️⃣ Importar Colección

**Opción A: Desde Archivo Local**
1. Click en `File` → `Import`
2. Selecciona `postman_collection.json`
3. Click en `Import`

**Opción B: Desde URL (si el proyecto está en GitHub)**
1. Click en `File` → `Import`
2. Click en pestaña `Link`
3. Pega la URL del archivo `postman_collection.json`

**Opción C: Copiar y Pegar JSON**
1. Click en `Import`
2. Click en pestaña `Raw text`
3. Copia todo el contenido de `postman_collection.json`
4. Pega en el campo de texto
5. Click `Continue` → `Import`

---´´

## ⚙️ Configurar Environment

### Variables Automáticas
La colección incluye variables que se actualizan automáticamente después de login:

```
baseUrl = http://localhost:8000/api
apiKey = test-api-key
token = (se genera después de login)
userId = (se obtiene después de login)
privateChatId = (se obtiene después de crear chat privado)
```

### Configuración Manual (si es necesario)

1. Click en `Environment` (icono de ojo, arriba a la derecha)
2. Click en `Manage Environments`
3. Crea nuevo environment o edita existente:

```
{
  "baseUrl": "http://localhost:8000/api",
  "apiKey": "test-api-key",
  "token": "",
  "userId": "",
  "privateChatId": ""
}
```

---

## 🧪 Flujo de Testing Recomendado

### Paso 1: Login
1. Ve a `Autenticación` → `Login`
2. Modifica email/password según necesites
3. Click `Send`
4. ✅ El token se guardará automáticamente en `{{token}}`

### Paso 2: Verificar Home
1. Ve a `Ubicación y Perfil` → `Home`
2. Click `Send`
3. Verifica que devuelve usuarios cercanos

### Paso 3: Chat General
1. Ve a `Chat General` → `Obtener chat general`
2. Click `Send`
3. Ve a `Chat General` → `Enviar mensaje al general`
4. Click `Send`
5. Ve a `Chat General` → `Obtener mensajes del general`
6. Click `Send`

### Paso 4: Chat Privado
1. Ve a `Chats Privados` → `Invitar / crear chat privado`
2. Modifica `user_id` al usuario que desees invitar
3. Click `Send`
4. ✅ El chat ID se guardará automáticamente
5. Luego puedes usar `Listar chats privados` para ver activos

### Paso 5: Sociales
1. Prueba `Bloquear usuario`
2. Prueba `Seguir usuario`
3. Prueba `Solicitar amistad`
4. Etc.

---

## 📝 Modificar Requests

### Cambiar User ID
En cualquier request que requiera `user_id`:

```json
{
  "user_id": 2  // Cambia según necesites
}
```

### Cambiar Mensaje
En requests de envío de mensaje:

```json
{
  "text": "Tu mensaje aquí"
}
```

### Cambiar Ubicación
En `Ubicación y Perfil` → `Actualizar ubicación`:

```json
{
  "lat": "40.7150",  // Cambiar latitud
  "lng": "-74.0100"  // Cambiar longitud
}
```

---

## 🔐 Validación en Tests

Postman ejecuta automáticamente scripts de test después de cada request.

**Login** incluye script que guarda el token:
```javascript
if (pm.response.code === 200) {
  var jsonData = pm.response.json();
  pm.environment.set('token', jsonData.token);
  pm.environment.set('userId', jsonData.user.id);
}
```

**Invitar** guarda el ID del chat privado:
```javascript
if (pm.response.code === 201 || pm.response.code === 200) {
  var jsonData = pm.response.json();
  pm.environment.set('privateChatId', jsonData.chat.id);
}
```

---

## 🐛 Troubleshooting

### ❌ "Invalid API Key"
**Solución:** Verifica que `X-API-KEY: test-api-key` esté en los headers

### ❌ "Token required"
**Solución:** 
- Primero ejecuta `Login`
- Verifica que el token se guardó en environment
- Espera 1 segundo antes de usar otros endpoints

### ❌ "Not a member of this chat"
**Solución:** 
- Crea un chat privado primero (endpoint `Invitar`)
- Usa el ID devuelto en otros requests

### ❌ "User not found"
**Solución:** 
- Verifica que el usuario existe
- Los usuarios de prueba tienen IDs 1, 2, 3, 4
- Para crear nuevos, usa `Crear usuario`

### ❌ "Chat not found"
**Solución:**
- Chat general tiene ID=1 (siempre disponible)
- Chats privados se crean con endpoint `Invitar`

---

## 💡 Tips

### Guardar Variables Manualmente
Si necesitas guardar una variable de respuesta:

1. Click en `Tests` (pestaña en el editor de request)
2. Escribe código que capture la variable:

```javascript
var jsonData = pm.response.json();
pm.environment.set('miVariable', jsonData.data.id);
```

### Usar Ejemplos Pre-guardados
La colección incluye ejemplos con datos reales de prueba.

**Usuarios de prueba:**
- user1@example.com / password
- user2@example.com / password
- user3@example.com / password
- user4@example.com / password

### Pre-request Scripts
Algunos requests ejecutan scripts antes de enviarse para validar que el token existe:

Click en `Pre-request Script` para verlos.

---

## 📚 Documentación Adicional

- [TESTING.md](./TESTING.md) - Ejemplos con curl
- [API_DOCUMENTATION.md](./API_DOCUMENTATION.md) - Especificación completa
- [README.md](./README.md) - Guía rápida
- [IMPLEMENTATION.md](./IMPLEMENTATION.md) - Resumen de implementación

---

## 🚀 Alternativas a Postman

Si prefieres otras herramientas:

### Insomnia
1. Click en `Create` → `Import`
2. Selecciona `From URL` o `From File`
3. Carga `postman_collection.json`

### Thunder Client (VS Code)
1. Abre VS Code
2. Instala extensión `Thunder Client`
3. Click derecho en archivo → `Open with Thunder Client`

### cURL (Línea de Comandos)
Ver ejemplos en [TESTING.md](./TESTING.md)

---

**¡Listo para probar la API!** 🎉
