Deployment

This document explains how to deploy the Bishal Starter Kit to a production server.

The starter kit is built with:

Laravel
PHP
Vue 3
Inertia.js
Vite
Tailwind CSS
Laravel Jetstream
MySQL or compatible database
Server Requirements

Before deploying, make sure the server supports the Laravel version used by the starter kit.

Recommended production environment:

PHP
Composer
Node.js
NPM
MySQL / MariaDB
Git
Web Server

The server should also have the PHP extensions required by Laravel and the packages installed by the starter kit.

1. Create the Project

The starter kit can be installed using Composer.

composer create-project bishalchy/bishal-start-kit my-project

Move into the project:

cd my-project
2. Configure Environment

Copy the example environment file:

cp .env.example .env

Generate the Laravel application key:

php artisan key:generate
3. Configure Application URL

Update .env:

APP_NAME="Bishal Starter Kit"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://example.com

For production, always use:

APP_DEBUG=false

Do not enable debug mode on a public production server.

4. Configure Database

Configure the production database in .env.

Example:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=starter_kit
DB_USERNAME=starter_user
DB_PASSWORD=your-password

Then run migrations:

php artisan migrate --force

The --force option allows migrations to run in production mode.

5. Install Composer Dependencies

Install production dependencies:

composer install --no-dev --optimize-autoloader

For a normal development environment:

composer install
6. Install NPM Dependencies

Install frontend dependencies:

npm install

For a production deployment, dependencies can also be installed using:

npm ci

when a valid package-lock.json is committed.

7. Build Frontend Assets

The starter kit uses Vite for frontend asset compilation.

Run:

npm run build

This generates the production frontend assets.

Do not use:

npm run dev

as the production frontend process.

8. Storage Link

If the application uses Laravel's public storage disk, create the symbolic link:

php artisan storage:link

This makes files stored under:

storage/app/public

accessible through:

public/storage
9. Cache Configuration

After configuring .env, optimize the Laravel application:

php artisan config:cache

Cache routes:

php artisan route:cache

Cache views:

php artisan view:cache

You can also run:

php artisan optimize
10. Clear Existing Cache

If the application was previously deployed with cached configuration, clear the caches before rebuilding them:

php artisan optimize:clear

Then:

php artisan optimize
11. File Permissions

Laravel needs write access to:

storage/
bootstrap/cache/

Example Linux permissions:

sudo chown -R www-data:www-data storage bootstrap/cache

Then:

sudo chmod -R 775 storage bootstrap/cache

The exact web-server user may differ.

For example:

www-data
nginx
apache

Use the user that runs PHP-FPM/web-server processes on your server.

12. Web Server Document Root

The web server must point to the Laravel:

public/

directory.

Do not point the domain directly to the Laravel project root.

Correct:

/home/user/my-project/public

Incorrect:

/home/user/my-project

This prevents sensitive files such as .env from being exposed.

Apache Configuration

A typical Apache virtual host looks like:

<VirtualHost *:80>
    ServerName example.com
    ServerAlias www.example.com

    DocumentRoot /var/www/my-project/public

    <Directory /var/www/my-project/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/my-project-error.log
    CustomLog ${APACHE_LOG_DIR}/my-project-access.log combined
</VirtualHost>

Enable the required Apache modules:

sudo a2enmod rewrite

Then restart Apache:

sudo systemctl restart apache2
Nginx Configuration

A typical Nginx configuration:

server {
    listen 80;
    server_name example.com www.example.com;

    root /var/www/my-project/public;

    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php-fpm.sock;
    }

    location ~ /\.ht {
        deny all;
    }
}

The exact PHP-FPM socket depends on the PHP version installed on the server.

HTTPS

Production applications should use HTTPS.

Example:

https://example.com

After configuring SSL, update:

APP_URL=https://example.com

If using HTTPS, make sure the web server redirects HTTP traffic to HTTPS.

Production Environment

A typical production .env should contain:

APP_ENV=production
APP_DEBUG=false
APP_URL=https://example.com

Do not commit .env to Git.

The .env file contains sensitive configuration such as:

Database credentials
Application key
Mail credentials
API keys
Backup configuration
Other secrets
Mail Configuration

If the application sends emails, configure the mail settings in .env.

Example:

MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@example.com"
MAIL_FROM_NAME="${APP_NAME}"

The starter kit also contains application-level mail settings associated with an organization.

Queue Configuration

If the application uses queued jobs, configure a queue worker on the production server.

Example:

php artisan queue:work

For production, the queue worker should normally be managed by a process manager such as Supervisor.

Example Supervisor configuration:

[program:starter-kit-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/my-project/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/my-project/storage/logs/worker.log

After changing Supervisor configuration:

sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl restart starter-kit-worker:*

Only configure queue workers if the application actually uses queued jobs.

Scheduler

If the starter kit uses scheduled tasks, configure Laravel's scheduler.

Add a cron entry:

* * * * * cd /var/www/my-project && php artisan schedule:run >> /dev/null 2>&1

Laravel's scheduler will determine which scheduled commands need to run.

Automatic Backup

The starter kit uses Spatie Laravel Backup for backup functionality.

Make sure the production environment has the required backup configuration.

A backup can be tested using:

php artisan backup:run

To clean old backups:

php artisan backup:clean

For scheduled backups, configure Laravel's scheduler or an appropriate cron process.

See:

Backup

Storage and Backup Permissions

The backup process needs access to the configured backup destination.

Make sure the Laravel process can read:

storage/

and any directories that need to be backed up.

Production Database Migration

When deploying a new version containing migrations:

php artisan migrate --force

Do not manually modify production database tables when the change should be represented by a Laravel migration.

Deployment Update

When deploying a new version from Git:

git pull origin main

Install PHP dependencies:

composer install --no-dev --optimize-autoloader

Install/build frontend dependencies:

npm ci
npm run build

Run migrations:

php artisan migrate --force

Clear and rebuild Laravel caches:

php artisan optimize:clear
php artisan optimize

If queues are used, restart workers:

php artisan queue:restart
Recommended Deployment Sequence

A typical deployment sequence is:

git pull origin main

composer install --no-dev --optimize-autoloader

npm ci
npm run build

php artisan migrate --force

php artisan storage:link

php artisan optimize:clear
php artisan optimize

php artisan queue:restart

The queue restart command is only required if queue workers are being used.

Shared Hosting / cPanel

The starter kit can be deployed to a hosting environment such as cPanel if the server provides the required PHP, Composer, Node.js/NPM, database, and Laravel capabilities.

The domain's document root should point to:

public/

For example:

/home/username/my-project/public

Avoid exposing:

.env
composer.json
composer.lock
package.json
storage/
vendor/

directly through the public web root.

cPanel Deployment

A typical deployment workflow is:

composer create-project bishalchy/bishal-start-kit my-project

Configure .env:

APP_ENV=production
APP_DEBUG=false
APP_URL=https://example.com

Configure database:

DB_DATABASE=database_name
DB_USERNAME=database_user
DB_PASSWORD=database_password

Run:

php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan optimize

Build frontend assets:

npm install
npm run build

If Node.js is not available on the hosting server, build the frontend assets on a development/build machine and deploy the generated assets with the application.

Environment Variables

Important production variables include:

APP_NAME=
APP_ENV=
APP_KEY=
APP_DEBUG=
APP_URL=

DB_CONNECTION=
DB_HOST=
DB_PORT=
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

MAIL_MAILER=
MAIL_HOST=
MAIL_PORT=
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=
MAIL_FROM_ADDRESS=
MAIL_FROM_NAME=

Additional variables may be required depending on the features and packages enabled in the application.

Production Security Checklist

Before making the application publicly accessible, verify:

[ ] APP_ENV=production
[ ] APP_DEBUG=false
[ ] APP_KEY is configured
[ ] APP_URL uses HTTPS
[ ] Database credentials are correct
[ ] public/ is the document root
[ ] .env is not publicly accessible
[ ] storage permissions are correct
[ ] bootstrap/cache permissions are correct
[ ] Frontend assets have been built
[ ] Database migrations are complete
[ ] Storage link exists
[ ] Mail configuration is tested
[ ] Backup configuration is tested
[ ] Queue workers are running if required
[ ] Scheduler is configured if required
Troubleshooting
500 Internal Server Error

Check:

php artisan optimize:clear

Then inspect:

storage/logs/laravel.log

Also verify:

.env
PHP version
PHP extensions
file permissions
database connection
CSS or JavaScript Not Loading

Run:

npm install
npm run build

Then verify that the generated assets exist under the Vite build directory.

Also check:

APP_URL=https://example.com
Database Connection Error

Verify:

DB_HOST=
DB_PORT=
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

Then test:

php artisan migrate:status
Permission Denied

Check:

storage/
bootstrap/cache/

Example:

sudo chmod -R 775 storage bootstrap/cache

Also verify the owner:

sudo chown -R www-data:www-data storage bootstrap/cache
Route Not Working

Clear cached routes:

php artisan route:clear

Then rebuild:

php artisan route:cache

Make sure the web server points to:

public/
Deployment Optimization

For production, the following commands are recommended after deployment:

composer install --no-dev --optimize-autoloader

php artisan config:cache
php artisan route:cache
php artisan view:cache

Or:

php artisan optimize

Frontend assets should be compiled using:

npm run build
Updating the Starter Kit

When updating the application:

git pull

Then:

composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan optimize:clear
php artisan optimize

Always review migrations and application changes before applying them to a production database.

Production Deployment Summary

The basic deployment process is:

composer create-project bishalchy/bishal-start-kit my-project

cd my-project

cp .env.example .env

php artisan key:generate

# Configure .env

composer install --no-dev --optimize-autoloader

npm install
npm run build

php artisan migrate --force

php artisan storage:link

php artisan optimize