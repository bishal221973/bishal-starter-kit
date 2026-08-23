# Installation

## Requirements

Before installing the Bishal Starter Kit, make sure your system has:

* PHP
* Composer
* Node.js
* NPM
* A supported database such as MySQL
* Git

## Install Using Composer

The recommended way to create a new Bishal Starter Kit project is with Composer.

Run:

```bash
composer create-project bishalchy/bishal-start-kit my-project
```

Replace `my-project` with the name you want to use for your application.

For example:

```bash
composer create-project bishalchy/bishal-start-kit blog
```

Then move into the project:

```bash
cd blog
```

## Configure Environment

Copy the example environment file:

```bash
cp .env.example .env
```

Generate the Laravel application key:

```bash
php artisan key:generate
```

Open `.env` and configure your database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=root
DB_PASSWORD=
```

Make sure the database exists before running the migrations.

## Run Migrations

Run the database migrations:

```bash
php artisan migrate
```

If the starter kit includes database seeders, you can also run:

```bash
php artisan migrate --seed
```

## Install Frontend Dependencies

Install the JavaScript dependencies:

```bash
npm install
```

## Start the Application

Start the Laravel development server:

```bash
php artisan serve
```

In a separate terminal, start Vite:

```bash
npm run dev
```

The application will normally be available at:

```text
http://127.0.0.1:8000
```

## Production Build

When preparing the application for production, build the frontend assets:

```bash
npm run build
```

## Complete Installation

A typical fresh installation looks like this:

```bash
composer create-project bishalchy/bishal-start-kit my-project

cd my-project

cp .env.example .env

php artisan key:generate

php artisan migrate

npm install

npm run dev
```

Then, in another terminal:

```bash
php artisan serve
```

## Troubleshooting

### Composer Cannot Find the Package

Make sure Composer can access Packagist and that the package name is correct:

```bash
composer create-project bishalchy/bishal-start-kit my-project
```

### Database Connection Error

Check the database configuration in `.env`:

```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=root
DB_PASSWORD=
```

Make sure your database server is running and the database exists.

### Application Key Missing

Run:

```bash
php artisan key:generate
```

### Frontend Assets Are Not Loading

Make sure Vite is running:

```bash
npm run dev
```

For production:

```bash
npm run build
```

## Next Steps

After installation, you can configure and customize:

* Authentication
* Dashboard
* Users
* Roles and permissions
* Theme colors
* Sidebar
* Vue components
* Inertia.js
* Tailwind CSS
* Vite
* Deployment
