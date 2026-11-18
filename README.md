# Definición del proyecto

Proyecto de evaluación. Forma parte de la evaluación técnica para el equipo de desarrollo.

## Versión Live

En seguida se describen configuraciones y primeros pasos, sin embargo, una versión "live" de este proyecto se puede usar desde: https://test.hugovhs.dev

Para ingresar al dashboard como admin https://test.hugovhs.dev/dashboard

Credenciales:
usuario: hugo_vhs@msn.com
contraseña: admin123

## Instalación y primeros pasos

- Crear base de datos
- Añadir base de datos, usuario y contraseña al .env
- Ejecutar `composer install` para instalar dependencias
- Ejecutar migraciones `php artisan migrate`
- Ejecutar `npm install` para instalar componentes Tailwind, iconos y poder generar estilos
- Ejecutar `npm run build` para generar estilos.

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

## Imágenes de productos

Ejecutar `php artisan storage:link` para que se puedan guardar/ver las imágenes

## API para tipo de cambio

Para consultar el tipo de cambio se usa la API https://api.frankfurter.dev/v2/rate/USD/MXN, la cual es publica y no requiere token. Se creo un servicio con la lógica solicitada para guardar/consultar en redis y ahorrar llamadas a la API.

## API de pagos

Se genero una API dentro de este mismo proyecto con el endpoint "api/pagos", se envían los datos de la tarjeta y responde con un success o fail.