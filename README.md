# PZSISTEMS USER SYSTEM

Sistema web desarrollado en PHP y MySQL para la gestión de perfiles de usuario.

Sistema CRUD de usuarios desarrollado con arquitectura cliente-servidor utilizando PHP y MySQL, permitiendo el registro, autenticación y administración segura de perfiles de usuario mediante sesiones y validaciones.

Además, el sistema incorpora un panel administrativo con manejo de roles para la gestión completa de usuarios.

---

## Funcionalidades

### Usuarios

- Registro de usuarios
- Inicio de sesión seguro
- Dashboard de perfil
- Actualización de datos personales
- Cambio de contraseña
- Tema claro y oscuro
- Avatar por defecto y subida de fotografía
- Validación de formularios
- Visualización y ocultamiento de contraseña
- Alertas modernas con SweetAlert
- Logout y cierre seguro de sesión

---

### Administrador

- Panel administrativo
- Gestión completa de usuarios
- Agregar usuarios
- Editar usuarios
- Eliminar usuarios
- Control de roles
- Control de estado de usuarios
- Administración sin necesidad de phpMyAdmin

---

## Tecnologías utilizadas

- PHP
- MySQL
- HTML5
- CSS3
- JavaScript
- XAMPP
- phpMyAdmin
- Git
- GitHub
- SweetAlert2

---

## Estructura del proyecto

```bash
perfil_usuario_php/
│
├── login.php
├── registro.php
├── perfil.php
├── actualizar_perfil.php
├── cambiar_password.php
├── logout.php
├── conexion.php
│
├── admin_panel.php
├── admin_agregar.php
├── admin_editar.php
├── admin_eliminar.php
│
├── img/
│   ├── logo.png
│   ├── avatar.png
│   ├── login-preview.png
│   ├── register-preview.png
│   └── profile-preview.png
│
├── uploads/
│
├── success.mp3
├── error.mp3
│
└── README.md
```

---

## Seguridad implementada

- Contraseñas cifradas con `password_hash()`
- Verificación segura mediante `password_verify()`
- Validación de formularios
- Prevención de usuarios duplicados
- Validación de correo electrónico
- Restricción de acceso mediante sesiones
- Protección de páginas privadas
- Validación de imágenes permitidas
- Uso de consultas preparadas (`prepare()`)
- Prevención básica de inyección SQL
- Control de acceso por roles
- Validación de sesión administrativa

---

## Capturas del sistema

### Login

![Login](img/login-preview.png)

---

### Registro

![Registro](img/register-preview.png)

---

### Perfil de usuario

![Perfil](img/profile-preview.png)

---

## Instalación

### 1. Clonar el repositorio

```bash
git clone https://github.com/paulzp-zavala/pzsistems-user-system.git
```

---

### 2. Copiar el proyecto en htdocs

```bash
C:\xampp\htdocs\
```

---

### 3. Iniciar Apache y MySQL desde XAMPP

---

### 4. Crear la base de datos

```sql
CREATE DATABASE perfil_usuario_db;
```

---

### 5. Crear tabla usuarios

```sql
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cedula VARCHAR(10) NOT NULL UNIQUE,
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    correo VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    foto VARCHAR(255) DEFAULT 'avatar.png',
    tema VARCHAR(20) DEFAULT 'light',
    rol VARCHAR(20) DEFAULT 'usuario',
    estado VARCHAR(20) DEFAULT 'activo',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

### 6. Crear usuario administrador

```sql
UPDATE usuarios
SET rol = 'admin'
WHERE correo = 'correo_del_administrador';
```

---

### 7. Ejecutar el sistema

Abrir en navegador:

```bash
http://localhost/perfil_usuario_php
```

---

## Flujo principal del sistema

```text
Registro
→ Login
→ Perfil de usuario
→ Actualización de datos
→ Cambio de contraseña
→ Logout

Administrador
→ Login admin
→ Panel administrativo
→ Agregar usuarios
→ Editar usuarios
→ Eliminar usuarios
```

---

## Autor

**Paul Zavala**  
Desarrollo Web - PHP y MySQL  
UTPL - 2026