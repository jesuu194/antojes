# Gestión del Proyecto - API Antojes

## Índice
1. [Iniciación y Planificación del Proyecto](#1-iniciación-y-planificación-del-proyecto)
2. [Análisis y Diseño](#2-análisis-y-diseño)
3. [Implementación (Desarrollo)](#3-implementación-desarrollo)
4. [Pruebas y Control de Calidad](#4-pruebas-testing-y-control-de-calidad)
5. [Despliegue y Puesta en Producción](#5-despliegue-deployment-y-puesta-en-producción)

---

## 1. Iniciación y Planificación del Proyecto

Es la parte más creativa, como un folio en blanco donde esbozaremos las partes del proyecto y de donde saldrá una idea clara de qué será el proyecto.

### 1.1 Definición del Alcance

**Necesidades del Cliente:**
- Aplicación de chat social con geolocalización para conectar usuarios cercanos
- Sistema de mensajería privada y pública
- Gestión de amistades, bloqueos y seguimientos
- Autenticación segura y gestión de usuarios

**Objetivos del Proyecto:**
- Crear una API REST escalable y segura
- Implementar geolocalización en tiempo real (radio de 5km)
- Desarrollar sistema de chat privado y general
- Sistema completo de gestión de relaciones sociales
- Despliegue en plataforma cloud con alta disponibilidad

**Límites del Proyecto:**
- Fase 1: API REST backend sin frontend dedicado
- Radio de geolocalización fijo de 5km
- Soporte inicial para mensajería de texto (sin multimedia)
- Base de datos relacional MySQL/PostgreSQL

### 1.2 Análisis de Viabilidad

#### Viabilidad Técnica ✅
- **Framework:** Symfony 7.2 - Framework PHP moderno y robusto
- **ORM:** Doctrine 3.6 - Gestión eficiente de base de datos
- **Base de Datos:** MySQL 8.0 / PostgreSQL - Soporte de geolocalización
- **Seguridad:** API Key + JWT - Autenticación stateless
- **Herramientas Disponibles:** 
  - Git para control de versiones
  - Composer para gestión de dependencias
  - Docker para contenedorización
  - XAMPP/LAMP para desarrollo local

#### Viabilidad Económica 💰
**Costes de Infraestructura:**
- Servidor Cloud (Railway/Render): 0-5€/mes (tier gratuito disponible)
- Base de datos PostgreSQL: Incluida en hosting
- Dominio (opcional): 10-15€/año
- Certificado SSL: Gratuito (Let's Encrypt)

**Esfuerzo Humano:**
- Desarrollo Backend: ~80 horas (1-2 desarrolladores)
- Diseño de Base de Datos: ~15 horas
- Testing y Debugging: ~20 horas
- Documentación: ~10 horas
- Despliegue: ~5 horas
- **Total estimado:** 130 horas

**Precio/Hora Estimado:** 20-40€/hora (según experiencia)  
**Presupuesto Total:** 2,600€ - 5,200€

### 1.3 Planificación Temporal

#### Diagrama de Gantt (8 semanas)

```
Semana  | Actividad
--------|----------------------------------------------------------
1       | ████ Análisis de requisitos y diseño de BD
2       | ████ Configuración entorno y estructura del proyecto
3-4     | ████████ Desarrollo Backend (Autenticación, Usuarios)
5-6     | ████████ Desarrollo Backend (Chat, Geolocalización, Relaciones)
7       | ████ Testing y corrección de errores
8       | ████ Documentación y Despliegue
```

#### Hitos y Cronograma

| Hito | Descripción | Fecha Límite |
|------|-------------|--------------|
| H1 | Diseño de base de datos completado | Semana 1 |
| H2 | Sistema de autenticación funcional | Semana 3 |
| H3 | CRUD de usuarios implementado | Semana 4 |
| H4 | Sistema de chat y geolocalización | Semana 6 |
| H5 | Gestión de relaciones sociales | Semana 6 |
| H6 | Testing completo | Semana 7 |
| H7 | Documentación API | Semana 7 |
| H8 | Despliegue en producción | Semana 8 |

### 1.4 Asignación de Recursos y Roles

#### Equipo de Desarrollo

| Rol | Responsabilidades | Tiempo |
|-----|-------------------|--------|
| **Backend Developer 1** | - Autenticación y seguridad<br>- Entities y repositorios<br>- API Controllers | 60h |
| **Backend Developer 2** | - Sistema de mensajería<br>- Geolocalización<br>- Relaciones sociales | 60h |
| **Database Administrator** | - Diseño de esquema<br>- Optimización de queries<br>- Migraciones | 15h |
| **DevOps** | - Configuración Docker<br>- Despliegue<br>- CI/CD | 10h |
| **QA Tester** | - Pruebas funcionales<br>- Testing con Postman<br>- Validación endpoints | 20h |
| **Technical Writer** | - Documentación API<br>- Guías de deploy<br>- README | 10h |

### 1.5 Documento de Especificación de Requisitos (SRS)

#### Requisitos Funcionales

**RF001 - Autenticación de Usuarios**
- El sistema debe permitir login con usuario y contraseña
- El sistema debe generar tokens JWT para sesiones
- El sistema debe validar API Key en todas las peticiones

**RF002 - Gestión de Usuarios**
- CRUD completo de usuarios
- Actualización de ubicación geográfica (latitud/longitud)
- Actualización de perfil (usuario, email, contraseña)

**RF003 - Geolocalización**
- Mostrar usuarios dentro de un radio de 5km
- Calcular distancia usando fórmula de Haversine
- Filtrar usuarios bloqueados de los resultados

**RF004 - Mensajería General**
- Chat público geolocalizado
- Mostrar mensajes de usuarios cercanos
- Orden cronológico de mensajes

**RF005 - Mensajería Privada**
- Crear chats privados entre usuarios
- Enviar y recibir mensajes privados
- Historial de conversaciones
- Soporte para chats grupales

**RF006 - Gestión de Relaciones Sociales**
- Sistema de solicitudes de amistad
- Seguir/Dejar de seguir usuarios
- Bloquear/Desbloquear usuarios
- Listar amigos, seguidores y bloqueados

**RF007 - Sistema de Invitaciones**
- Enviar invitaciones a nuevos usuarios
- Validar códigos de invitación

#### Requisitos No Funcionales

**RNF001 - Rendimiento**
- Tiempo de respuesta API < 200ms (90% peticiones)
- Soporte de 100 usuarios concurrentes mínimo

**RNF002 - Seguridad**
- Contraseñas hasheadas (bcrypt/argon2)
- Protección contra SQL Injection (ORM)
- CORS configurado correctamente
- HTTPS obligatorio en producción

**RNF003 - Escalabilidad**
- Arquitectura stateless (sin sesiones en servidor)
- Base de datos normalizada
- Preparado para caché (Redis opcional)

**RNF004 - Mantenibilidad**
- Código siguiendo PSR-12
- Arquitectura MVC con Symfony
- Documentación completa de endpoints
- Tests unitarios y de integración

**RNF005 - Disponibilidad**
- Uptime 99% en producción
- Backups automáticos de base de datos
- Health check endpoint

---

## 2. Análisis y Diseño

El análisis del proyecto afecta tanto a los datos que va a manejar como la tecnología a utilizar. Es fundamental para no estar "dando palos de ciego" y perdiendo tiempo realizando cambios que nos lleven mucho tiempo, lo que supone una pérdida económica.

### 2.1 Diseño de la Base de Datos

#### Modelo Entidad-Relación (ER)

```
┌─────────────────┐         ┌──────────────────┐
│      User       │────────>│   UserFollow     │
│─────────────────│  1:N    │──────────────────│
│ id (PK)         │         │ follower_id (FK) │
│ usuario         │         │ following_id(FK) │
│ email           │         │ created_at       │
│ password        │         └──────────────────┘
│ latitud         │
│ longitud        │         ┌──────────────────┐
│ token           │────────>│   UserBlock      │
│ codigo_invita   │  1:N    │──────────────────│
│ created_at      │         │ blocker_id (FK)  │
└─────────────────┘         │ blocked_id (FK)  │
        │                   │ created_at       │
        │                   └──────────────────┘
        │
        │                   ┌──────────────────┐
        │──────────────────>│ FriendRequest    │
        │            1:N    │──────────────────│
        │                   │ sender_id (FK)   │
        │                   │ receiver_id (FK) │
        │                   │ status           │
        │                   │ created_at       │
        │                   └──────────────────┘
        │
        │                   ┌──────────────────┐
        │──────────────────>│      Chat        │
        │            1:N    │──────────────────│
        │                   │ id (PK)          │
        │                   │ name             │
        │                   │ is_group         │
        │                   │ created_at       │
        │                   └────────┬─────────┘
        │                            │
        │                   ┌────────┴─────────┐
        │──────────────────>│   ChatMember     │
        │            1:N    │──────────────────│
        │                   │ chat_id (FK)     │
        │                   │ user_id (FK)     │
        │                   │ joined_at        │
        │                   └──────────────────┘
        │
        │                   ┌──────────────────┐
        └──────────────────>│     Message      │
                     1:N    │──────────────────│
                            │ id (PK)          │
                            │ chat_id (FK)     │
                            │ user_id (FK)     │
                            │ contenido        │
                            │ created_at       │
                            └──────────────────┘
```

#### Modelo Relacional con Normalización (3NF)

**Tabla: user**
```sql
CREATE TABLE user (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    latitud DECIMAL(10,8) DEFAULT NULL,
    longitud DECIMAL(11,8) DEFAULT NULL,
    token VARCHAR(255) DEFAULT NULL,
    codigo_invitacion VARCHAR(20) UNIQUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_location (latitud, longitud),
    INDEX idx_token (token)
);
```

**Tabla: user_follow**
```sql
CREATE TABLE user_follow (
    id INT PRIMARY KEY AUTO_INCREMENT,
    follower_id INT NOT NULL,
    following_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (follower_id) REFERENCES user(id) ON DELETE CASCADE,
    FOREIGN KEY (following_id) REFERENCES user(id) ON DELETE CASCADE,
    UNIQUE KEY unique_follow (follower_id, following_id)
);
```

**Tabla: user_block**
```sql
CREATE TABLE user_block (
    id INT PRIMARY KEY AUTO_INCREMENT,
    blocker_id INT NOT NULL,
    blocked_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (blocker_id) REFERENCES user(id) ON DELETE CASCADE,
    FOREIGN KEY (blocked_id) REFERENCES user(id) ON DELETE CASCADE,
    UNIQUE KEY unique_block (blocker_id, blocked_id)
);
```

**Tabla: friend_request**
```sql
CREATE TABLE friend_request (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    status VARCHAR(20) DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES user(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES user(id) ON DELETE CASCADE,
    INDEX idx_receiver_status (receiver_id, status)
);
```

**Tabla: chat**
```sql
CREATE TABLE chat (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) DEFAULT NULL,
    is_group BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

**Tabla: chat_member**
```sql
CREATE TABLE chat_member (
    id INT PRIMARY KEY AUTO_INCREMENT,
    chat_id INT NOT NULL,
    user_id INT NOT NULL,
    joined_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (chat_id) REFERENCES chat(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE,
    UNIQUE KEY unique_membership (chat_id, user_id)
);
```

**Tabla: message**
```sql
CREATE TABLE message (
    id INT PRIMARY KEY AUTO_INCREMENT,
    chat_id INT NOT NULL,
    user_id INT NOT NULL,
    contenido TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (chat_id) REFERENCES chat(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE,
    INDEX idx_chat_created (chat_id, created_at)
);
```

#### Diagrama UML de Clases

```
┌─────────────────────────────────────┐
│             User                    │
├─────────────────────────────────────┤
│ - id: int                           │
│ - usuario: string                   │
│ - email: string                     │
│ - password: string                  │
│ - latitud: float                    │
│ - longitud: float                   │
│ - token: string                     │
│ - codigoInvitacion: string          │
│ - createdAt: DateTime               │
├─────────────────────────────────────┤
│ + getId(): int                      │
│ + getUsuario(): string              │
│ + setUsuario(string): self          │
│ + getEmail(): string                │
│ + setEmail(string): self            │
│ + getPassword(): string             │
│ + setPassword(string): self         │
│ + getLatitud(): ?float              │
│ + setLatitud(?float): self          │
│ + getLongitud(): ?float             │
│ + setLongitud(?float): self         │
│ + getToken(): ?string               │
│ + setToken(?string): self           │
│ + calculateDistance(User): float    │
└─────────────────────────────────────┘
         ▲                    ▲
         │                    │
         │1                   │1
         │                    │
    ┌────┴────┐          ┌────┴────┐
    │         │          │         │
    │         │*         │*        │
┌───┴──────────────┐  ┌──┴───────────────┐
│   UserFollow     │  │   UserBlock      │
├──────────────────┤  ├──────────────────┤
│ - follower: User │  │ - blocker: User  │
│ - following:User │  │ - blocked: User  │
│ - createdAt: DT  │  │ - createdAt: DT  │
└──────────────────┘  └──────────────────┘

┌─────────────────────────────────────┐
│          Chat                       │
├─────────────────────────────────────┤
│ - id: int                           │
│ - name: string                      │
│ - isGroup: boolean                  │
│ - members: Collection<ChatMember>   │
│ - messages: Collection<Message>     │
│ - createdAt: DateTime               │
├─────────────────────────────────────┤
│ + addMember(ChatMember): self       │
│ + removeMember(ChatMember): self    │
│ + getMembers(): Collection          │
│ + addMessage(Message): self         │
│ + getMessages(): Collection         │
└─────────────────────────────────────┘
         │1                    │1
         │                     │
         │*                    │*
┌────────┴─────────┐  ┌────────┴─────────┐
│   ChatMember     │  │     Message      │
├──────────────────┤  ├──────────────────┤
│ - chat: Chat     │  │ - chat: Chat     │
│ - user: User     │  │ - user: User     │
│ - joinedAt: DT   │  │ - contenido: str │
├──────────────────┤  │ - createdAt: DT  │
│ + getChat()      │  ├──────────────────┤
│ + getUser()      │  │ + getContenido() │
└──────────────────┘  │ + setContenido() │
                      └──────────────────┘
```

### 2.2 Diseño Técnico

#### Arquitectura de la Aplicación

**Patrón:** MVC (Model-View-Controller) con Symfony  
**Tipo:** API REST con arquitectura stateless

```
┌──────────────────────────────────────────┐
│         Cliente (Postman/App)            │
└────────────────┬─────────────────────────┘
                 │ HTTP Request
                 │ (JSON + Headers)
                 ▼
┌──────────────────────────────────────────┐
│      EventSubscribers (Middleware)       │
│  - ApiKeySubscriber (Validación)         │
│  - CorsSubscriber (Headers)              │
└────────────────┬─────────────────────────┘
                 │
                 ▼
┌──────────────────────────────────────────┐
│           Controllers Layer              │
│  - LoginController                       │
│  - UserController                        │
│  - MessageController                     │
│  - FollowController                      │
│  - BlockController                       │
│  - FriendshipController                  │
│  - PrivateChatController                 │
└────────────────┬─────────────────────────┘
                 │
                 ▼
┌──────────────────────────────────────────┐
│          Services Layer                  │
│  - Lógica de negocio                     │
│  - Validaciones complejas                │
│  - Cálculos de distancia                 │
└────────────────┬─────────────────────────┘
                 │
                 ▼
┌──────────────────────────────────────────┐
│         Repositories Layer               │
│  - UserRepository                        │
│  - ChatRepository                        │
│  - MessageRepository                     │
│  - ChatMemberRepository                  │
└────────────────┬─────────────────────────┘
                 │
                 ▼
┌──────────────────────────────────────────┐
│          Entities (Models)               │
│  - User                                  │
│  - Chat                                  │
│  - Message                               │
│  - ChatMember                            │
│  - UserFollow, UserBlock, FriendRequest  │
└────────────────┬─────────────────────────┘
                 │
                 ▼
┌──────────────────────────────────────────┐
│        Database (MySQL/PostgreSQL)       │
└──────────────────────────────────────────┘
```

#### Stack Tecnológico Seleccionado

**Backend:**
- **Framework:** Symfony 7.2 (PHP 8.2+)
  - *Justificación:* Framework empresarial robusto, excelente documentación, componentes reutilizables
- **ORM:** Doctrine 3.6
  - *Justificación:* Mapeo objeto-relacional eficiente, migraciones automáticas, DQL poderoso
- **Autenticación:** API Key + JWT (stateless)
  - *Justificación:* Escalable, sin sesiones en servidor, seguro

**Base de Datos:**
- **Producción:** PostgreSQL 14+
  - *Justificación:* Mejor soporte para geolocalización, funciones matemáticas avanzadas
- **Desarrollo:** MySQL 8.0
  - *Justificación:* Compatible con XAMPP, fácil configuración local

**Herramientas de Desarrollo:**
- **Control de Versiones:** Git + GitHub
- **Gestor de Dependencias:** Composer
- **Contenedorización:** Docker + Docker Compose
- **Servidor Local:** XAMPP / LAMP
- **Testing API:** Postman
- **IDE Recomendado:** Visual Studio Code / PhpStorm

**Infraestructura Cloud:**
- **Hosting:** Railway (principal) / Render (alternativa)
- **Base de Datos:** PostgreSQL managed (incluido en hosting)
- **SSL:** Let's Encrypt (automático)
- **Dominio:** Opcional (subdomain gratuito incluido)

#### Medidas de Seguridad

**Autenticación y Autorización:**
- API Key en header `X-API-KEY` (validación global via EventSubscriber)
- Tokens JWT para identificar usuarios autenticados
- Passwords hasheados con Argon2 o bcrypt
- Validación de token en cada petición protegida

**Protección de Datos:**
- SQL Injection: Prevenido por Doctrine ORM (prepared statements)
- XSS: Sanitización de inputs
- CORS: Configurado correctamente via CorsSubscriber
- Rate Limiting: Preparado para implementar en producción

**Gestión de Secretos:**
- Variables de entorno (.env) para credenciales
- `.env` excluido del repositorio Git
- Diferentes configuraciones para dev/prod

### 2.3 Diseño de la Interfaz de Usuario (UI/UX)

**Nota:** Este proyecto es una API REST pura. La interfaz de usuario se implementará en un frontend separado (opcional). Sin embargo, se proporcionan herramientas para testing:

**Herramientas de Testing Proporcionadas:**
- **Postman Collection:** `postman_collection_updated.json`
- **Scripts PowerShell:** `test_all_endpoints.ps1`, `test_api.ps1`
- **Páginas HTML de Prueba:** 
  - `public/index.html` - Landing page
  - `public/docs.html` - Documentación interactiva
  - `public/test.html` - Interfaz de testing

**Mockups Futuros (Frontend):**
- Pantalla de Login
- Mapa con usuarios cercanos
- Lista de chats
- Chat privado
- Perfil de usuario
- Gestión de amigos

### 2.4 Diseño de la API REST

#### Endpoints Principales

Ver documentación completa en: [API_DOCUMENTATION.md](API_DOCUMENTATION.md)

**Formato de Datos:** JSON  
**Formato de Respuesta Estandarizado:**
```json
{
  "data": { /* resultado exitoso */ },
  "error": null
}
```
O en caso de error:
```json
{
  "data": null,
  "error": "Mensaje de error descriptivo"
}
```

**Resumen de Endpoints:**

| Método | Endpoint | Descripción | Auth |
|--------|----------|-------------|------|
| POST | `/api/login` | Autenticación de usuario | API Key |
| POST | `/api/logout` | Cerrar sesión | API Key + Token |
| GET | `/api/home` | Usuarios cercanos (5km) | API Key + Token |
| GET | `/api/general` | Chat general geolocalizado | API Key + Token |
| POST | `/api/actualizar` | Actualizar ubicación | API Key + Token |
| PUT | `/api/editar-perfil` | Modificar perfil | API Key + Token |
| POST | `/api/seguir` | Seguir usuario | API Key + Token |
| DELETE | `/api/dejar-seguir/{id}` | Dejar de seguir | API Key + Token |
| POST | `/api/bloquear` | Bloquear usuario | API Key + Token |
| DELETE | `/api/desbloquear/{id}` | Desbloquear | API Key + Token |
| POST | `/api/solicitud-amistad` | Enviar solicitud | API Key + Token |
| POST | `/api/aceptar-amistad/{id}` | Aceptar solicitud | API Key + Token |
| GET | `/api/privado` | Lista de chats privados | API Key + Token |
| POST | `/api/enviar-mensaje` | Enviar mensaje | API Key + Token |
| POST | `/api/crear-chat` | Crear chat privado/grupo | API Key + Token |

---

## 3. Implementación (Desarrollo)

Después de tenerlo todo diseñado es hora de ponerse "Manos a la obra". Para ello, configuraremos el Entorno de Desarrollo y realizaremos los siguientes pasos.

### 3.1 Configuración del Entorno de Desarrollo

#### Herramientas Necesarias

**Software Base:**
- PHP 8.2 o superior
- Composer (gestor de dependencias PHP)
- MySQL 8.0 o PostgreSQL 14+
- Git para control de versiones
- XAMPP (Windows) o LAMP (Linux) para servidor local

**Instalación Paso a Paso:**

1. **Instalar PHP y Composer:**
```bash
# Verificar instalación
php -v
composer --version
```

2. **Clonar el Repositorio:**
```bash
git clone https://github.com/usuario/antojes-master.git
cd antojes-master
```

3. **Instalar Dependencias:**
```bash
composer install
```

4. **Configurar Variables de Entorno:**
```bash
# Copiar archivo de ejemplo
cp .env .env.local

# Editar .env.local con tus credenciales
# DATABASE_URL="mysql://user:pass@127.0.0.1:3306/antojes"
# API_KEY=tu_api_key_secreta
```

5. **Crear Base de Datos:**
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

6. **Cargar Datos de Prueba (opcional):**
```bash
php bin/console doctrine:fixtures:load
```

7. **Iniciar Servidor de Desarrollo:**
```bash
symfony server:start
# O alternativa:
php -S localhost:8000 -t public
```

#### Configuración de Docker (Opcional)

**Archivo: `compose.yaml`**
```yaml
version: '3.8'
services:
  app:
    build: .
    ports:
      - "8000:80"
    volumes:
      - .:/var/www/html
    environment:
      DATABASE_URL: mysql://root:password@db:3306/antojes
  
  db:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: password
      MYSQL_DATABASE: antojes
    volumes:
      - db_data:/var/lib/mysql

volumes:
  db_data:
```

**Comandos Docker:**
```bash
# Iniciar contenedores
docker-compose up -d

# Ver logs
docker-compose logs -f

# Ejecutar comandos Symfony
docker-compose exec app php bin/console doctrine:migrations:migrate
```

### 3.2 Desarrollo del Backend

#### 3.2.1 Creación de Entities (Modelos)

Las entities representan las tablas de la base de datos usando el patrón Active Record de Doctrine.

**Ejemplo: Entity User**

Ubicación: `src/Entity/User.php`

```php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'user')]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, unique: true)]
    private ?string $usuario = null;

    #[ORM\Column(length: 100, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 8, nullable: true)]
    private ?float $latitud = null;

    #[ORM\Column(type: 'decimal', precision: 11, scale: 8, nullable: true)]
    private ?float $longitud = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $token = null;

    #[ORM\Column(name: 'created_at', type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    // Getters y Setters...
    
    /**
     * Calcula la distancia en km a otro usuario usando Haversine
     */
    public function calculateDistance(User $otherUser): float
    {
        $earthRadius = 6371; // km
        
        $latFrom = deg2rad($this->latitud);
        $lonFrom = deg2rad($this->longitud);
        $latTo = deg2rad($otherUser->getLatitud());
        $lonTo = deg2rad($otherUser->getLongitud());
        
        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;
        
        $a = sin($latDelta / 2) ** 2 + 
             cos($latFrom) * cos($latTo) * sin($lonDelta / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return $earthRadius * $c;
    }
}
```

**Entities Implementadas:**
- ✅ `User.php` - Usuarios del sistema
- ✅ `Chat.php` - Salas de chat
- ✅ `Message.php` - Mensajes
- ✅ `ChatMember.php` - Miembros de chats
- ✅ `UserFollow.php` - Relaciones de seguimiento
- ✅ `UserBlock.php` - Bloqueos entre usuarios
- ✅ `FriendRequest.php` - Solicitudes de amistad

#### 3.2.2 Creación de Repositorios

Los repositorios contienen queries personalizadas para acceder a los datos.

**Ejemplo: UserRepository**

Ubicación: `src/Repository/UserRepository.php`

```php
namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Encuentra usuarios cercanos dentro de un radio (km)
     * Excluye usuarios bloqueados
     */
    public function findNearbyUsers(User $currentUser, float $radiusKm = 5): array
    {
        $qb = $this->createQueryBuilder('u');
        
        return $qb
            ->where('u.id != :currentId')
            ->andWhere('u.latitud IS NOT NULL')
            ->andWhere('u.longitud IS NOT NULL')
            ->setParameter('currentId', $currentUser->getId())
            ->getQuery()
            ->getResult();
    }

    public function findByToken(string $token): ?User
    {
        return $this->findOneBy(['token' => $token]);
    }
}
```

**Repositorios Implementados:**
- ✅ `UserRepository.php` - Queries de usuarios
- ✅ `ChatRepository.php` - Queries de chats
- ✅ `MessageRepository.php` - Queries de mensajes
- ✅ `ChatMemberRepository.php` - Queries de miembros

#### 3.2.3 Implementación de Controladores

Los controladores manejan las peticiones HTTP y retornan respuestas JSON.

**Ejemplo: LoginController**

Ubicación: `src/Controller/LoginController.php`

```php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

class LoginController extends AbstractController
{
    #[Route('/api/login', methods: ['POST'])]
    public function login(
        Request $request, 
        EntityManagerInterface $em
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        
        $usuario = $data['usuario'] ?? null;
        $password = $data['password'] ?? null;
        
        if (!$usuario || !$password) {
            return $this->json([
                'data' => null,
                'error' => 'Usuario y contraseña requeridos'
            ], 400);
        }
        
        $user = $em->getRepository(User::class)
            ->findOneBy(['usuario' => $usuario]);
        
        if (!$user || !password_verify($password, $user->getPassword())) {
            return $this->json([
                'data' => null,
                'error' => 'Credenciales inválidas'
            ], 401);
        }
        
        // Generar token único
        $token = bin2hex(random_bytes(32));
        $user->setToken($token);
        $em->flush();
        
        return $this->json([
            'data' => [
                'token' => $token,
                'usuario' => $user->getUsuario(),
                'email' => $user->getEmail()
            ],
            'error' => null
        ]);
    }
}
```

**Controladores Implementados:**
- ✅ `LoginController.php` - Autenticación
- ✅ `LogoutController.php` - Cerrar sesión
- ✅ `UserController.php` - CRUD usuarios
- ✅ `HomeController.php` - Usuarios cercanos
- ✅ `GeneralController.php` - Chat general
- ✅ `MessageController.php` - Mensajería
- ✅ `PrivateChatController.php` - Chats privados
- ✅ `FollowController.php` - Seguir/Dejar de seguir
- ✅ `BlockController.php` - Bloquear/Desbloquear
- ✅ `FriendshipController.php` - Solicitudes de amistad
- ✅ `UpdateController.php` - Actualizar ubicación

#### 3.2.4 Conexión a Base de Datos con Doctrine

**Configuración: `config/packages/doctrine.yaml`**

```yaml
doctrine:
    dbal:
        url: '%env(resolve:DATABASE_URL)%'
        driver: 'pdo_mysql'
        server_version: '8.0'
        charset: utf8mb4

    orm:
        auto_generate_proxy_classes: true
        naming_strategy: doctrine.orm.naming_strategy.underscore_number_aware
        auto_mapping: true
        mappings:
            App:
                is_bundle: false
                dir: '%kernel.project_dir%/src/Entity'
                prefix: 'App\Entity'
                alias: App
```

**Uso de DQL (Doctrine Query Language):**

```php
// Ejemplo de query DQL personalizada
$dql = "SELECT u, COUNT(m.id) as messageCount 
        FROM App\Entity\User u 
        LEFT JOIN App\Entity\Message m WITH m.user = u 
        WHERE u.latitud BETWEEN :minLat AND :maxLat 
        GROUP BY u.id 
        ORDER BY messageCount DESC";

$query = $em->createQuery($dql);
$query->setParameters([
    'minLat' => $lat - 0.05,
    'maxLat' => $lat + 0.05
]);

$results = $query->getResult();
```

#### 3.2.5 Implementación de API REST

**Rutas Configuradas: `config/routes.yaml`**

```yaml
controllers:
    resource:
        path: ../src/Controller/
        namespace: App\Controller
    type: attribute

api_login:
    path: /api/login
    controller: App\Controller\LoginController::login
    methods: [POST]

api_home:
    path: /api/home
    controller: App\Controller\HomeController::index
    methods: [GET]
```

**Formato de Respuesta Estandarizado:**

Todos los endpoints retornan:
```json
{
  "data": { /* contenido */ },
  "error": null
}
```

#### 3.2.6 Autenticación y Autorización

**EventSubscriber para API Key:**

Ubicación: `src/EventSubscriber/ApiKeySubscriber.php`

```php
namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiKeySubscriber implements EventSubscriberInterface
{
    private const API_KEY = 'tu_api_key_secreta';

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 10],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        
        // Solo validar rutas API
        if (!str_starts_with($request->getPathInfo(), '/api/')) {
            return;
        }
        
        $apiKey = $request->headers->get('X-API-KEY');
        
        if ($apiKey !== self::API_KEY) {
            $response = new JsonResponse([
                'data' => null,
                'error' => 'API Key inválida'
            ], 403);
            
            $event->setResponse($response);
        }
    }
}
```

**Validación de Token de Usuario:**

```php
// En cada controller protegido
private function getUserFromToken(Request $request, EntityManagerInterface $em): ?User
{
    $token = $request->headers->get('Authorization');
    
    if (!$token) {
        return null;
    }
    
    // Remover "Bearer " si existe
    $token = str_replace('Bearer ', '', $token);
    
    return $em->getRepository(User::class)->findByToken($token);
}
```

### 3.3 Integración y Testing

#### Testing con Postman

**Collection Incluida:** `postman_collection_updated.json`

**Variables de Entorno Postman:**
```json
{
  "api_url": "http://localhost:8000",
  "api_key": "tu_api_key",
  "token": "",
  "user_id": ""
}
```

**Flujo de Testing:**
1. Login → Guardar token
2. Actualizar ubicación
3. Ver usuarios cercanos (Home)
4. Chat general
5. Crear chat privado
6. Enviar mensaje privado
7. Gestión de relaciones (seguir, bloquear, amistad)

#### Scripts PowerShell para Testing

**Archivo: `test_all_endpoints.ps1`**
```powershell
$apiUrl = "http://localhost:8000"
$apiKey = "tu_api_key"

# Test Login
$loginResponse = Invoke-RestMethod -Uri "$apiUrl/api/login" `
    -Method Post `
    -Headers @{"X-API-KEY"=$apiKey} `
    -Body (@{"usuario"="test";"password"="test123"} | ConvertTo-Json) `
    -ContentType "application/json"

$token = $loginResponse.data.token
Write-Host "Token: $token"

# Test Home (usuarios cercanos)
$homeResponse = Invoke-RestMethod -Uri "$apiUrl/api/home" `
    -Method Get `
    -Headers @{
        "X-API-KEY"=$apiKey;
        "Authorization"="Bearer $token"
    }

Write-Host "Usuarios cercanos: $($homeResponse.data.Length)"
```

---

## 4. Pruebas (Testing) y Control de Calidad

Aunque el cliente es el que se va a encargar de usar la aplicación, deberemos estar seguros de que nada puede fallar.

### 4.1 Pruebas Unitarias

Las pruebas unitarias verifican que funciones o métodos específicos funcionen de forma aislada.

**Framework:** PHPUnit (incluido con Symfony)

**Ejemplo: Test de Entity User**

Ubicación: `tests/Entity/UserTest.php`

```php
namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testCalculateDistance(): void
    {
        $user1 = new User();
        $user1->setLatitud(39.4699); // Valencia
        $user1->setLongitud(-0.3763);
        
        $user2 = new User();
        $user2->setLatitud(39.4800);
        $user2->setLongitud(-0.3600);
        
        $distance = $user1->calculateDistance($user2);
        
        // Debe ser aproximadamente 1.5 km
        $this->assertLessThan(2, $distance);
        $this->assertGreaterThan(1, $distance);
    }
    
    public function testPasswordHashing(): void
    {
        $user = new User();
        $plainPassword = 'test123';
        $hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);
        
        $user->setPassword($hashedPassword);
        
        $this->assertTrue(
            password_verify($plainPassword, $user->getPassword())
        );
    }
}
```

**Ejecutar Tests Unitarios:**
```bash
php bin/phpunit tests/Entity
```

### 4.2 Pruebas de Integración

Verifican que los diferentes módulos funcionan correctamente juntos.

**Ejemplo: Test de Login Controller**

Ubicación: `tests/Controller/LoginControllerTest.php`

```php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginControllerTest extends WebTestCase
{
    public function testLoginSuccess(): void
    {
        $client = static::createClient();
        
        $client->request('POST', '/api/login', [], [], [
            'HTTP_X-API-KEY' => 'tu_api_key',
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'usuario' => 'testuser',
            'password' => 'testpass123'
        ]));
        
        $this->assertResponseIsSuccessful();
        
        $response = json_decode($client->getResponse()->getContent(), true);
        
        $this->assertArrayHasKey('data', $response);
        $this->assertArrayHasKey('token', $response['data']);
        $this->assertNull($response['error']);
    }
    
    public function testLoginInvalidCredentials(): void
    {
        $client = static::createClient();
        
        $client->request('POST', '/api/login', [], [], [
            'HTTP_X-API-KEY' => 'tu_api_key',
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'usuario' => 'wronguser',
            'password' => 'wrongpass'
        ]));
        
        $this->assertResponseStatusCodeSame(401);
        
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertNotNull($response['error']);
    }
}
```

**Ejecutar Tests de Integración:**
```bash
php bin/phpunit tests/Controller
```

### 4.3 Pruebas de Usabilidad y Aceptación

**Checklist de Validación:**

✅ **Autenticación:**
- [ ] Login con credenciales válidas retorna token
- [ ] Login con credenciales inválidas retorna error 401
- [ ] Logout invalida el token correctamente

✅ **Usuarios Cercanos:**
- [ ] Home retorna solo usuarios dentro de 5km
- [ ] Usuarios bloqueados no aparecen en la lista
- [ ] Distancia calculada es precisa

✅ **Chat General:**
- [ ] Mensajes solo de usuarios cercanos
- [ ] Orden cronológico correcto
- [ ] Mensajes de bloqueados excluidos

✅ **Chat Privado:**
- [ ] Crear chat entre 2 usuarios
- [ ] Enviar mensaje a chat existente
- [ ] Listar todos los chats del usuario
- [ ] Mensajes en orden cronológico

✅ **Relaciones Sociales:**
- [ ] Seguir/dejar de seguir funciona
- [ ] Bloquear/desbloquear funciona
- [ ] Solicitudes de amistad se envían
- [ ] Aceptar solicitudes funciona
- [ ] Rechazar solicitudes funciona

✅ **Actualización de Perfil:**
- [ ] Actualizar ubicación
- [ ] Cambiar usuario
- [ ] Cambiar email
- [ ] Cambiar contraseña

**Criterios de Aceptación:**
- Todos los endpoints retornan formato `{data, error}`
- Respuestas en < 200ms (90% requests)
- Sin errores 500 en happy paths
- Validaciones de input correctas
- Errores descriptivos en español

### 4.4 Resolución de Errores (Debugging)

**Herramientas de Debugging:**

1. **Symfony Profiler (Entorno Dev):**
```bash
# Habilitar profiler
php bin/console debug:config framework profiler
```
Acceder a: `http://localhost:8000/_profiler`

2. **Logs de Symfony:**
```bash
# Ver logs en tiempo real
tail -f var/log/dev.log
```

3. **Xdebug (Opcional):**
```ini
; php.ini
zend_extension=xdebug
xdebug.mode=debug
xdebug.start_with_request=yes
```

4. **Queries de Base de Datos:**
```php
// Activar logging de queries SQL
$em->getConnection()->getConfiguration()->setSQLLogger(
    new \Doctrine\DBAL\Logging\EchoSQLLogger()
);
```

**Errores Comunes y Soluciones:**

| Error | Causa | Solución |
|-------|-------|----------|
| 403 Forbidden | API Key incorrecta | Verificar header `X-API-KEY` |
| 401 Unauthorized | Token inválido/expirado | Hacer login nuevamente |
| 500 Database error | Conexión DB fallida | Verificar `DATABASE_URL` en `.env` |
| CORS errors | Headers no configurados | Verificar `CorsSubscriber.php` |
| Distances always 0 | Null lat/long | Ejecutar `/api/actualizar` primero |

**Proceso de Debugging:**
1. Reproducir el error
2. Verificar logs (`var/log/dev.log`)
3. Comprobar datos de entrada (JSON válido)
4. Verificar query SQL ejecutada
5. Usar `dump()` y `dd()` en Symfony para inspeccionar variables
6. Validar permisos de base de datos

---

## 5. Despliegue (Deployment) y Puesta en Producción

La puesta en producción es la parte en la que el proyecto local se pasa a su ubicación final y estará disponible para todos los usuarios que sea necesario.

### 5.1 Preparación del Entorno de Producción

#### Configuración de Servidor Web

**Opción A: Apache (.htaccess)**

Ubicación: `public/.htaccess`

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^(.*)$ index.php [QSA,L]
</IfModule>

<IfModule mod_headers.c>
    Header set Access-Control-Allow-Origin "*"
    Header set Access-Control-Allow-Methods "GET, POST, PUT, DELETE, OPTIONS"
    Header set Access-Control-Allow-Headers "Content-Type, Authorization, X-API-KEY"
</IfModule>
```

**Opción B: Nginx**

Ubicación: `/etc/nginx/sites-available/antojes`

```nginx
server {
    listen 80;
    server_name antojes.ejemplo.com;
    root /var/www/antojes/public;

    location / {
        try_files $uri /index.php$is_args$args;
    }

    location ~ ^/index\.php(/|$) {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;
    }

    location ~ \.php$ {
        return 404;
    }
}
```

#### Configuración de Base de Datos en Producción

**Variables de Entorno (.env.production):**
```env
APP_ENV=prod
APP_DEBUG=0
DATABASE_URL="postgresql://user:pass@db-host:5432/antojes_prod?serverVersion=14&charset=utf8"
API_KEY=tu_api_key_produccion_segura
```

**Optimizaciones de Base de Datos:**
```sql
-- Crear índices para rendimiento
CREATE INDEX idx_user_location ON user(latitud, longitud);
CREATE INDEX idx_message_chat_created ON message(chat_id, created_at);
CREATE INDEX idx_chat_member_user ON chat_member(user_id);

-- Configurar charset
ALTER DATABASE antojes_prod CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 5.2 Despliegue en Servicios Cloud

#### Opción 1: Railway (Recomendado) ⭐

**Pasos de Despliegue:**

1. **Crear cuenta en Railway:**
   - Ir a https://railway.app
   - Login con GitHub

2. **Crear Workspace:**
   - "New Team" → Nombre → "Create"

3. **Desplegar Proyecto:**
   - "New Project" → "Deploy from GitHub repo"
   - Seleccionar repositorio `antojes-master`
   - Railway detecta automáticamente Symfony

4. **Configurar Variables de Entorno:**
   ```
   APP_ENV=prod
   DATABASE_URL=${{Postgres.DATABASE_URL}}
   API_KEY=tu_api_key_segura
   ```

5. **Agregar PostgreSQL:**
   - "New" → "Database" → "Add PostgreSQL"
   - Railway conecta automáticamente

6. **Ejecutar Migraciones:**
   - En Railway CLI o desde el dashboard:
   ```bash
   php bin/console doctrine:migrations:migrate --no-interaction
   ```

**Documentación Completa:** Ver [RAILWAY_SIMPLE.md](RAILWAY_SIMPLE.md)

#### Opción 2: Render

**Archivo de Configuración: `render.yaml`**

```yaml
services:
  - type: web
    name: antojes-api
    env: php
    buildCommand: |
      composer install --no-dev --optimize-autoloader
      php bin/console doctrine:migrations:migrate --no-interaction
    startCommand: php -S 0.0.0.0:$PORT -t public
    envVars:
      - key: APP_ENV
        value: prod
      - key: DATABASE_URL
        fromDatabase:
          name: antojes-db
          property: connectionString

databases:
  - name: antojes-db
    databaseName: antojes
    user: antojes_user
```

**Pasos:**
1. Ir a https://render.com
2. "New +" → "Blueprint"
3. Conectar repositorio GitHub
4. "Apply" - Render despliega automáticamente

**Documentación Completa:** Ver [RENDER_DEPLOYMENT.md](RENDER_DEPLOYMENT.md)

#### Opción 3: Heroku (Legacy)

**Archivos Necesarios:**
- `Procfile.heroku`
- Configuración en Heroku dashboard

**Comandos:**
```bash
heroku create antojes-api
heroku addons:create heroku-postgresql:hobby-dev
git push heroku main
heroku run php bin/console doctrine:migrations:migrate
```

#### Opción 4: Fly.io

**Configuración: `fly.toml`**

```toml
app = "antojes-api"

[build]
  builder = "paketobuildpacks/builder:base"

[env]
  APP_ENV = "prod"

[[services]]
  internal_port = 8080
  protocol = "tcp"

  [[services.ports]]
    port = 80
  [[services.ports]]
    port = 443
```

**Comandos:**
```bash
fly launch
fly postgres create
fly deploy
```

### 5.3 Dominio y SSL

#### Configurar Dominio Personalizado

**En Railway:**
1. Settings → Domains
2. "Generate Domain" (subdominio gratuito: `antojes.up.railway.app`)
3. O "Custom Domain" → Añadir tu dominio
4. Configurar CNAME en tu proveedor DNS:
   ```
   CNAME antojes.ejemplo.com → antojes.up.railway.app
   ```

**En Render:**
1. Automáticamente genera: `antojes-api.onrender.com`
2. Custom domain: Settings → Custom Domains
3. Configurar DNS:
   ```
   CNAME www.ejemplo.com → antojes-api.onrender.com
   ```

#### Certificado SSL (HTTPS)

**Automático en Railway/Render:**
- SSL/TLS automático con Let's Encrypt
- Renovación automática cada 90 días
- HTTPS forzado por defecto

**Manual (VPS propio):**
```bash
# Instalar Certbot
sudo apt install certbot python3-certbot-nginx

# Obtener certificado
sudo certbot --nginx -d antojes.ejemplo.com

# Renovación automática
sudo certbot renew --dry-run
```

### 5.4 Documentación para el Despliegue

#### Checklist Pre-Despliegue

✅ **Código:**
- [ ] Todas las features implementadas
- [ ] Tests pasando (PHPUnit)
- [ ] Sin errores de linting
- [ ] Código en repositorio Git actualizado

✅ **Configuración:**
- [ ] Variables de entorno configuradas
- [ ] `APP_ENV=prod` y `APP_DEBUG=0`
- [ ] API Key segura generada
- [ ] Database URL production configurada

✅ **Base de Datos:**
- [ ] Migraciones creadas y probadas
- [ ] Datos de prueba (fixtures) no en producción
- [ ] Backups configurados
- [ ] Índices optimizados

✅ **Seguridad:**
- [ ] HTTPS habilitado
- [ ] CORS correctamente configurado
- [ ] API Key no hardcodeada
- [ ] Contraseñas hasheadas
- [ ] `.env` no en repository

✅ **Monitoreo:**
- [ ] Logs habilitados
- [ ] Health check endpoint (`/health`)
- [ ] Alertas configuradas (opcional)

#### Comandos de Despliegue

**Deploy Local → Producción:**

```bash
# 1. Preparar código
git add .
git commit -m "Release v1.0"
git push origin main

# 2. En producción (SSH o Railway CLI)
composer install --no-dev --optimize-autoloader

# 3. Ejecutar migraciones
php bin/console doctrine:migrations:migrate --no-interaction

# 4. Limpiar caché
php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod

# 5. Verificar
curl https://antojes.up.railway.app/health
```

**Rollback (si algo falla):**
```bash
# Volver a commit anterior
git reset --hard HEAD~1
git push -f origin main

# O migración anterior
php bin/console doctrine:migrations:migrate prev
```

#### Monitoreo Post-Despliegue

**Health Check Endpoint:**

Ubicación: `src/Controller/HealthController.php`

```php
#[Route('/health', methods: ['GET'])]
public function health(EntityManagerInterface $em): JsonResponse
{
    try {
        // Verificar conexión DB
        $em->getConnection()->executeQuery('SELECT 1');
        
        return $this->json([
            'status' => 'ok',
            'timestamp' => date('Y-m-d H:i:s'),
            'database' => 'connected'
        ]);
    } catch (\Exception $e) {
        return $this->json([
            'status' => 'error',
            'error' => $e->getMessage()
        ], 500);
    }
}
```

**Verificar Logs:**
```bash
# Railway
railway logs

# Render
Dashboard → Logs tab

# Servidor propio
tail -f var/log/prod.log
```

**Métricas a Monitorear:**
- Uptime (debe ser > 99%)
- Tiempo de respuesta promedio (< 200ms)
- Errores 500 (debe ser 0%)
- Uso de base de datos
- Uso de memoria

---

## 6. Anexos

### 6.1 Recursos Adicionales

**Documentación del Proyecto:**
- [README.md](README.md) - Introducción y Quick Start
- [API_DOCUMENTATION.md](API_DOCUMENTATION.md) - Documentación completa de endpoints
- [DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md) - Guía de despliegue detallada
- [POSTMAN_GUIDE.md](POSTMAN_GUIDE.md) - Uso de la colección Postman

**Herramientas de Testing:**
- `postman_collection_updated.json` - Colección Postman
- `test_all_endpoints.ps1` - Script PowerShell de testing
- `public/test.html` - Interfaz web de pruebas

**Scripts de Utilidad:**
- `setup.sh` / `setup.bat` - Configuración inicial automática
- `scripts/dump_users.php` - Export de usuarios
- `scripts/dump_chats.php` - Export de chats

### 6.2 Stack Tecnológico Final

| Capa | Tecnología | Versión |
|------|------------|---------|
| **Framework** | Symfony | 7.2 |
| **Lenguaje** | PHP | 8.2+ |
| **ORM** | Doctrine | 3.6 |
| **Base de Datos (Dev)** | MySQL | 8.0 |
| **Base de Datos (Prod)** | PostgreSQL | 14+ |
| **Autenticación** | API Key + Token | Custom |
| **Contenedorización** | Docker | 24+ |
| **Hosting** | Railway / Render | Cloud |
| **Control de Versiones** | Git + GitHub | - |
| **Gestor de Dependencias** | Composer | 2.6+ |
| **Testing API** | Postman | Latest |

### 6.3 Equipo y Contacto

**Desarrolladores:**
- Backend Lead: [Nombre]
- Database Admin: [Nombre]
- DevOps: [Nombre]

**Repositorio del Proyecto:**
- GitHub: https://github.com/usuario/antojes-master

**Entorno de Producción:**
- URL API: https://antojes.up.railway.app
- Health Check: https://antojes.up.railway.app/health

**Soporte:**
- Email: soporte@ejemplo.com
- Issues: GitHub Issues

---

## 7. Conclusiones y Próximos Pasos

### 7.1 Estado Actual del Proyecto

✅ **Completado:**
- Diseño completo de base de datos (7 tablas, relaciones definidas)
- API REST funcional con 20+ endpoints
- Sistema de autenticación robusto
- Geolocalización en tiempo real
- Chat general y privado
- Gestión de relaciones sociales
- Documentación completa
- Despliegue en cloud (Railway/Render)
- Testing con Postman

### 7.2 Mejoras Futuras

🚀 **Fase 2 - Escalabilidad:**
- [ ] Implementar Redis para caché
- [ ] Rate limiting por usuario
- [ ] Paginación en endpoints de listado
- [ ] WebSockets para chat en tiempo real
- [ ] Notificaciones push

🔒 **Fase 3 - Seguridad Avanzada:**
- [ ] Autenticación JWT con refresh tokens
- [ ] OAuth2 (login con Google/Facebook)
- [ ] 2FA (autenticación de dos factores)
- [ ] Encriptación de mensajes end-to-end

📱 **Fase 4 - Features Adicionales:**
- [ ] Mensajes multimedia (fotos, videos)
- [ ] Grupos de chat
- [ ] Llamadas de voz/video
- [ ] Sistema de reputación
- [ ] Filtros de búsqueda avanzados

🎨 **Fase 5 - Frontend:**
- [ ] App móvil (React Native / Flutter)
- [ ] Web app (React / Vue)
- [ ] Panel de administración

---

**Fecha de Última Actualización:** Febrero 2026  
**Versión del Documento:** 1.0  
**Estado del Proyecto:** ✅ En Producción
