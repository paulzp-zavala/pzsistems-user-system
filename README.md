# PZSISTEMS USER SYSTEM

Sistema web desarrollado en PHP y MySQL para la gestión de perfiles de usuario.

Sistema CRUD de usuarios desarrollado con arquitectura cliente-servidor utilizando PHP y MySQL, permitiendo el registro, autenticación y administración segura de perfiles de usuario mediante sesiones y validaciones.

---

## Funcionalidades

- Registro de usuarios
- Inicio de sesión seguro
- Dashboard de perfil
- Actualización de datos personales
- Cambio de contraseña
- Validación de formularios
- Tema claro y oscuro
- Avatar por defecto y subida de foto
- Alertas modernas con SweetAlert
- Seguridad con contraseñas cifradas
- Validación de duplicados
- Visualización y ocultamiento de contraseña

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
├── conexion.php
├── logout.php
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

### 5. Crear la tabla usuarios

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
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

### 6. Ejecutar el sistema

Abrir en el navegador:

```bash
http://localhost/perfil_usuario_php
```

---

## Autor

**Paul Andres Zavala Palomeque**  
Desarrollo Web - PHP y MySQL  
UTPL - 2026

---

## Repositorio GitHub

[Repositorio del proyecto](https://github.com/paulzp-zavala/pzsistems-user-system?utm_source=chatgpt.com)