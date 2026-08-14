# Sistema de Gestión de Pedidos (Laravel)

Este es un módulo de gestión y procesamiento de pedidos desarrollado con Laravel. Permite administrar productos, registrar compras calculando totales en el servidor y ver desgloses históricos mediante transacciones seguras de Base de Datos.

## 🛠️ Tecnologías utilizadas

- **Framework:** Laravel 13.x
- **Entorno de Servidor:** XAMPP-Lite v8.5.5 (PHP 8.5.x)
- **Base de datos:** MySQL / MariaDB (Incluida en XAMPP)
- **Frontend:** Blade Templates, Tailwind CSS (Componentes Flux)

## 🚀 Requisitos del sistema

- XAMPP-Lite v8.5.5 o superior (con PHP habilitado)
- Composer instalado globalmente
- Node.js & NPM

## 💻 Instalación y Configuración

Siga estos pasos para ejecutar el proyecto de forma local dentro de su entorno de desarrollo:

1. **Clonar el repositorio:**
   Coloque el proyecto dentro de su carpeta de servidor local (ej. `C:\xampp_lite_8_5\www\laravel\`) y clone el repositorio:
   ```bash
   git clone https://github.com
   cd prueba-api-pedidos
   ```

2. **Instalar dependencias de PHP:**
   ```bash
   composer install
   ```

3. **Instalar dependencias de Frontend:**
   ```bash
   npm install && npm run dev
   ```

4. **Configurar el archivo de entorno:**
   Duplique el archivo `.env.example` y renombrelo a `.env`:
   ```bash
   cp .env.example .env
   ```
   Abra el archivo `.env` y configure las credenciales apuntando a su base de datos local de XAMPP:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=prueba
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Generar la clave de la aplicación:**
   ```bash
   php artisan key:generate
   ```

6. **Ejecutar Migraciones y Seeders:**
   Este comando creará todas las tablas necesarias (incluyendo la tabla pivote `order_product` con las columnas calculadas `price` y `subtotal`):
   ```bash
   php artisan migrate --seed
   ```

7. **Iniciar el servidor local:**
   Asegúrese de que el módulo MySQL esté encendido en su panel de XAMPP y ejecute:
   ```bash
   php artisan serve
   ```
   Acceda a la aplicación mediante: `http://127.0.0.1:8000`

## 📦 Características implementadas

- **Gestión de Pedidos:** Listado de órdenes con paginación (`orders.index`).
- **Transacciones Seguras:** Creación de pedidos encapsulada en `DB::transaction` en el backend para evitar registros huérfanos o inconsistencias de stock.
- **Cálculo Automático:** El backend calcula subtotales y totales de forma automatizada consultando directamente el precio vigente del producto.
- **Filtros en Formulario:** Limpieza automática de arreglos para descartar solicitudes con cantidad en `0`.
- **Vista de Detalles:** Desglose del pedido con la información del usuario, cantidades compradas y el precio congelado del producto al momento de la compra (`order_product.pivot`).
