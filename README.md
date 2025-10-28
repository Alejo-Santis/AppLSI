# AppLSI - Sistema de Gestión de Recursos Humanos

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Inertia.js](https://img.shields.io/badge/Inertia.js-000000?style=for-the-badge&logo=inertia&logoColor=white)
![Svelte](https://img.shields.io/badge/Svelte-5-FF3E00?style=for-the-badge&logo=svelte)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-316192?style=for-the-badge&logo=postgresql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)

## Descripción del Proyecto

AppLSI es un sistema de gestión de recursos humanos desarrollado con Laravel 12, Inertia.js y Svelte 5. Esta aplicación permite gestionar empleados, departamentos, puestos de trabajo, documentos y más, todo a través de una interfaz intuitiva y fácil de usar, construida con Bootstrap 5 para un diseño responsivo y moderno.

## 🚀 Tecnologías Principales

- **Backend**: Laravel 12.x
- **Frontend**: Inertia.js con Svelte 5
- **Base de datos**: PostgreSQL
- **Autenticación**: Laravel Breeze
- **UI/UX**: Bootstrap 5
- **Control de Acceso**: Spatie Laravel Permission
- **Exportación/Importación de Datos**: Maatwebsite/Excel
- **Notificaciones**: SweetAlert2

## 📋 Requisitos del Sistema

- PHP 8.2 o superior
- Composer
- Node.js 20+ y NPM
- PostgreSQL 14+
- Servidor web (Apache/Nginx)

## 🛠️ Instalación

1. **Clonar el repositorio**

   ```bash
   git clone [URL_DEL_REPOSITORIO]
   cd AppLSI
   ```

2. **Instalar dependencias de PHP**

   ```bash
   composer install
   ```

3. **Instalar dependencias de Node.js**

   ```bash
   npm install
   ```

4. **Configurar el entorno**

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configurar la base de datos**
   Editar el archivo `.env` con las credenciales de tu base de datos.

6. **Ejecutar migraciones y seeders**

   ```bash
   php artisan migrate --seed
   ```

7. ***Crear enlace simbólico para la carpeta storage***

   ```bash
   php artisan storage:link
   ```

8. ***Darle permi

9. **Compilar assets**

   ```bash
   npm run dev
   # o para producción
   npm run build
   ```

10. **Iniciar el servidor**

   ```bash
   php artisan serve
   ```

## 🔐 Credenciales por defecto

- **Email**: <admin@example.com>
- **Contraseña**: password

## 📚 Módulos Principales

### 1. Autenticación y Perfil de Usuario

- Inicio de sesión
- Registro de nuevos usuarios
- Recuperación de contraseña
- Perfil de usuario
- Actualización de información personal
- Cambio de contraseña

### 2. Gestión de Departamentos

- Listado de departamentos
- Creación de departamentos
- Edición de departamentos
- Eliminación de departamentos
- Exportación/Importación de datos

### 3. Gestión de Puestos

- Listado de puestos
- Creación de puestos
- Edición de puestos
- Eliminación de puestos
- Exportación/Importación de datos

### 4. Gestión de Empleados

- Listado de empleados
- Creación de empleados
- Visualización de detalles del empleado
- Edición de empleados
- Documentación asociada
- Contactos de emergencia
- Historial salarial

### 5. Gestión de Proyectos

- Listado de proyectos
- Creación de proyectos
- Asignación de empleados a proyectos
- Seguimiento de progreso

### 6. Gestión de Documentos de los Empleados

- Listado de documentos
- Creación de documentos
- Edición de documentos
- Eliminación de documentos
- Exportación/Importación de datos

### 7. Gestión de Contactos de Emergencia

- Listado de contactos
- Creación de contactos
- Edición de contactos
- Eliminación de contactos
- Exportación/Importación de datos

### 8. Gestión de Historial Salarial

- Listado de salarios
- Creación de salarios
- Edición de salarios
- Eliminación de salarios
- Exportación/Importación de datos

### 9. Gestión de Roles y Permisos

- Listado de roles
- Creación de roles
- Edición de roles
- Eliminación de roles

### 10. Gestión de Permisos

- Listado de permisos
- Creación de permisos
- Edición de permisos
- Eliminación de permisos

## 🧩 Características Adicionales

- Interfaz responsiva
- Exportación a Excel
- Importación desde Excel
- Búsqueda y filtrado avanzado
- Roles y permisos
- Panel de administración
- Notificaciones en tiempo real

## 📄 Licencia

Este proyecto está bajo la licencia [MIT](LICENSE).

## 🤝 Contribución

Las contribuciones son bienvenidas. Por favor, lee las [pautas de contribución](CONTRIBUTING.md) antes de enviar un pull request.

## ✉️ Contacto

Para consultas o soporte, por favor contacta al equipo de desarrollo.
