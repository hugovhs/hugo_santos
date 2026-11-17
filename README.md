# Definición del proyecto

Proyecto de evaluación. Forma parte de la evaluación técnica para el equipo de desarrollo.

## Instalación y primeros pasos

- Crear base de datos
- Añadir base de datos, usuario y contraseña al .env
- Ejecutar migraciones `php artisan migrate`

## Crear el primero usuario administrador

Para poder ingresar al dashboard es necesario contar con credenciales válidas, para agilizar y hacer más fácil el proceso se ha generado un comando artisan, ejecuta:

``` bash
php artisan admin:generate
```

Después de introducir los datos solicitados, se genera el acceso al dashboard.

## Redis

Asegurarse de tener instalado redis-server
Asegurarse de tener la extensiónn php-redis.
Configurar servidor redis en .env, normalmente la configuración base de Laravel ya funciona
