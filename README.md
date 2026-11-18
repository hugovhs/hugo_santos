# NutriViva

Tienda en línea desarrollada como evaluación técnica. Incluye catálogo de productos, carrito, compra como invitado y un panel para administrar productos y categorías.

## Demo

- Tienda: [test.hugovhs.dev](https://test.hugovhs.dev)
- Panel: [test.hugovhs.dev/dashboard](https://test.hugovhs.dev/dashboard)
- Usuario: `hugo_vhs@msn.com`
- Contraseña: `admin123`

## Requisitos

- PHP 8.2 o superior, Composer y las extensiones necesarias para Laravel y la base de datos elegida.
- Node.js y npm para compilar los recursos con Vite.
- MySQL o una base de datos compatible configurada en Laravel.
- Redis y la extensión PHP `phpredis` para consultar y almacenar el tipo de cambio.

El proyecto utiliza Laravel 12, Livewire 3, Tailwind CSS 4 y Vite.

## Instalación local

1. Instala las dependencias y crea el archivo de configuración:

   ```bash
   composer install
   cp .env.example .env
   php artisan key:generate
   ```

2. Configura la base de datos en `.env`. Para usar SQLite, la opción predeterminada, crea el archivo:

   ```bash
   touch database/database.sqlite
   ```

   Para MySQL o MariaDB, crea la base de datos y configura `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` y `DB_PASSWORD`.

3. Configura Redis en `.env` y asegúrate de que el servidor esté en ejecución. Ajusta `REDIS_HOST`, `REDIS_PORT` y `REDIS_PASSWORD` según tu entorno. Configura también la URL del tipo de cambio:

   ```dotenv
   EXCHANGE_RATE_API=https://api.frankfurter.dev/v2/rate/USD/MXN
   ```

   La URL incluida en `.env.example` es distinta; el servicio espera una respuesta JSON con un campo numérico `rate`.

4. Prepara las tablas, el acceso a imágenes y los recursos:

   ```bash
   php artisan migrate
   php artisan storage:link
   npm install
   npm run build
   ```

5. Crea un administrador siguiendo las indicaciones del comando:

   ```bash
   php artisan admin:generate
   ```

6. Inicia la aplicación:

   ```bash
   PHP_CLI_SERVER_WORKERS=4 php artisan serve
   ```

   Abre [http://localhost:8000](http://localhost:8000) o entra al panel en `/dashboard`.
   
El checkout llama a la API de pagos de la misma aplicación; el servidor local necesita poder atender esa petición mientras procesa la compra. Ajusta `APP_URL` si utilizas otra dirección o puerto.

Para desarrollar los estilos y scripts con recarga automática, ejecuta `npm run dev` en otra terminal en lugar de depender de la compilación de `npm run build` que se usa para producción.

## Funcionalidades

- Catálogo y detalle de productos, con carrito de compra.
- Compra como invitado con correo, dirección y pago simulado. Al generar el pedido se descuenta el inventario disponible.
- Panel protegido por autenticación para gestionar productos y categorías, con imágenes y búsqueda de productos.
- Conversión entre USD y MXN mediante `ExchangeService`. Redis conserva la tasa durante seis horas; si una consulta HTTP no devuelve una tasa válida, se utiliza la última tasa almacenada, si existe.

## Pago simulado

`POST /api/pagos` valida los datos de tarjeta y simula una respuesta: tiene un 70 % de probabilidad de éxito y una demora de tres segundos. No conecta con una pasarela real; utiliza únicamente datos ficticios para probarlo. El checkout envía la solicitud a esta ruta usando `APP_URL`.
