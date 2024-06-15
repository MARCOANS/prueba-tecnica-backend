
# Laravel Project

## Descripción

Este es un proyecto de Laravel, un framework de PHP para el desarrollo de aplicaciones web.

## Requisitos previos

- PHP (versión 8.1 o superior)
- Composer (versión 2.x o superior)

## Configuración del proyecto

1. Clona este repositorio en tu máquina local.

   ```bash
   git clone https://github.com/tu-usuario/tu-repositorio.git
   ```

2. Navega al directorio del proyecto.

   ```bash
   cd tu-repositorio
   ```

3. Copia el archivo de ejemplo de configuración de entorno y renómbralo a `.env`.

   ```bash
   cp .env.example .env
   ```

4. Genera la clave de la aplicación.

   ```bash
   php artisan key:generate
   ```

## Instalación de dependencias

1. Instala las dependencias del proyecto utilizando Composer.

   ```bash
   composer install
   ```

## Scripts disponibles

En el directorio del proyecto, puedes ejecutar los siguientes comandos:

### `php artisan serve`

Inicia el servidor de desarrollo de Laravel.
Abre [http://localhost:8000](http://localhost:8000) para verlo en tu navegador.

### `composer test`

Ejecuta las pruebas unitarias utilizando PHPUnit.

### `php artisan migrate`

Ejecuta las migraciones de la base de datos.

### `php artisan db:seed`

Rellena la base de datos con datos de prueba utilizando los seeders.

## Dependencias principales

- [Laravel Framework](https://laravel.com/)
- [Guzzle HTTP](https://github.com/guzzle/guzzle)
- [Laravel Sanctum](https://laravel.com/docs/8.x/sanctum)
- [Laravel Tinker](https://github.com/laravel/tinker)

## Dependencias de desarrollo

- [Faker PHP](https://github.com/FakerPHP/Faker)
- [Laravel Pint](https://github.com/laravel/pint)
- [Laravel Sail](https://laravel.com/docs/8.x/sail)
- [Mockery](https://github.com/mockery/mockery)
- [Nunomaduro Collision](https://github.com/nunomaduro/collision)
- [PHPUnit](https://phpunit.de/)
- [Spatie Laravel Ignition](https://spatie.be/docs/laravel-ignition/v2/introduction)

## Licencia

Este proyecto está licenciado bajo la Licencia MIT.
