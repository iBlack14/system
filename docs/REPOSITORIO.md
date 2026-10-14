# Repositorio privado de Acotec

El README original conserva los derechos y restricciones del código base. Este proyecto debe alojarse en un repositorio privado, sin redistribuir públicamente el código del curso.

## Qué versionar

Código Laravel, vistas y recursos de interfaz, dependencias declaradas y sus archivos de bloqueo, migraciones, pruebas y documentación. `.env.example` es una plantilla sin credenciales.

## Qué no subir

`.env` ni sus variantes locales, credenciales, claves, bases SQLite, copias SQL, archivos ZIP, adjuntos de `public/uploads`, contenido de `storage/app`, sesiones, cachés y logs. Tampoco se versionan `vendor` ni `node_modules`.

Las exclusiones no borran ningún archivo del equipo. Git no reemplaza el respaldo privado de la base de datos y los adjuntos; deben respaldarse por separado con controles de acceso.

## Instalación en otro equipo

1. Clonar el repositorio privado con una cuenta autorizada.
2. Instalar las dependencias de `composer.lock` y `package-lock.json` mediante Composer y npm.
3. Crear `.env` a partir de `.env.example` y configurar las credenciales localmente. Nunca compartirlas en el repositorio.
4. Restaurar una copia autorizada de la base de datos y de los adjuntos por un canal privado. Revisar previamente las migraciones existentes; no ejecutar `migrate:fresh` sobre datos reales.
5. Configurar la clave de aplicación y los permisos de escritura de `storage` y `bootstrap/cache` según el entorno. No reemplazar una clave existente si hay datos cifrados que dependan de ella.
6. Compilar recursos con `npm run build`, preparar las vistas con `php artisan view:cache` e iniciar el servidor del entorno.

Para publicar el código falta configurar el destino remoto, autenticar la cuenta propietaria y definir el nombre y correo del autor de los commits. No enviar contraseñas ni tokens por chat.
