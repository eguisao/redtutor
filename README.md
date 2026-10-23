# REDTUTOR

Plataforma de tutorías voluntarias. Esta primera fase incluye pantalla pública, autenticación con Auth0, sincronización de usuario a MySQL y panel autenticado con sidebar.

## Requisitos

- PHP 7.3+
- Composer
- MySQL
- XAMPP / Apache
- Cuenta de Auth0 con una aplicación cliente configurada

## Estructura

- app/config: configuración de entorno y base de datos
- app/controllers: controladores MVC
- app/models: acceso a datos
- app/views: templates HTML
- public: punto de entrada web y assets

## Configuración

1. Copia `.env.example` a `.env`.
2. Completa los valores de Auth0 y MySQL.
3. Importa la base de datos del esquema indicado.
4. Configura el host virtual o usa la carpeta `public` como raíz del sitio.

## Puntos de entrada

- `/` -> pantalla pública
- `/login` -> inicia flujo Auth0
- `/callback` -> retorno Auth0
- `/logout` -> cierre de sesión
- `/dashboard` -> área autenticada
- `/materias` -> módulo inicial de materias

## Notas

- No se almacenan contraseñas de usuario en MySQL.
- El identificador principal de enlace es `auth0_id`.
- Los perfiles se manejan como `1 = Tutor` y `2 = Estudiante`.
