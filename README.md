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

## Corrección de seguridad aplicada

Se corrigió un hallazgo de seguridad detectado por análisis estático en los recursos externos cargados desde CDN: faltaban los atributos de integridad `integrity` y `crossorigin="anonymous"` en los enlaces y scripts de Bootstrap, Google Fonts y SweetAlert2.

### Cambio realizado

- Se agregaron atributos `integrity` con hashes SHA-384 a todos los archivos CSS y JS cargados desde CDNs.
- Se añadieron `crossorigin="anonymous"` para cumplir con la validación de integridad del navegador.
- Se aplicó la corrección en las vistas principales: dashboard, usuarios, materias y home público.

### Resultado

- Los recursos externos se validan contra su contenido real antes de cargarse.
- Se reduce el riesgo de manipulación o sustitución de librerías por contenido alterado.
- Se elimina la advertencia de integridad de recursos en el contexto identificado.

## Corrección aplicada para ejecución en CLI y pruebas

Se corrigió un problema de estabilidad detectado durante la generación de cobertura con PHPUnit. El código de arranque de la aplicación estaba iniciando sesión incluso cuando se ejecutaba desde línea de comandos, lo que provocaba advertencias y errores al generar reportes de cobertura.

### Cambio realizado

- Se protegió la llamada a `session_start()` para que no se ejecute en entornos CLI.
- Se añadió validación para evitar iniciar sesión cuando los headers ya fueron enviados.
- Se mantuvo el comportamiento normal en la aplicación web, donde la sesión sigue arrancando correctamente.

### Resultado

- PHPUnit puede ejecutarse sin fallos en contexto de pruebas automatizadas.
- La generación de cobertura no se interrumpe por la sesión del navegador.
- Se evita el error `session_start(): Session cannot be started after headers have already been sent` en procesos no web.

## Verificación realizada

Se ejecutó la prueba unitaria real del modelo `Auth0UserModel` con PHPUnit y se obtuvo resultado correcto:

- `OK (5 tests, 21 assertions)`

Esto confirma que la lógica del proyecto se valida con pruebas reales, no con pruebas matemáticas triviales, y que la corrección aplicada no rompe la funcionalidad esperada.

## Notas

- No se almacenan contraseñas de usuario en MySQL.
- El identificador principal de enlace es `auth0_id`.
- Los perfiles se manejan como `1 = Tutor` y `2 = Estudiante`.
