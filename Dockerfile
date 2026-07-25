FROM php:8.5-fpm

# Install system dependencies PHP needs to talk to MySQL/Postgres, handle images, etc.
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    libpq-dev

# Node.js — needed to actually build the frontend assets (Tailwind/Filament
# CSS+JS). Without this step, the app boots fine but every page 500s with
# "Vite manifest not found", since public/build/manifest.json never gets
# created — that's exactly what was happening before this was added.
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Install the actual PHP extensions your app uses
RUN docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip intl

# Opcache — without this, PHP recompiles every file from source on every
# single request instead of reusing compiled bytecode. This is one of the
# most common causes of a slow Dockerized Laravel app, and it was missing
# entirely before. validate_timestamps=0 is safe here specifically because
# Render rebuilds this image fresh on every deploy — there's no scenario
# where stale cached bytecode from an old file version could linger.
COPY opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Composer (PHP's package manager) — copied in from its own official image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy your project files into the container
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Install JS dependencies and actually build the assets — this is the step
# that was missing entirely before, causing the Vite manifest error
RUN npm ci && npm run build

# Laravel needs these folders to be writable
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 8000

# config:cache + route:cache + view:cache matter more for Filament than a
# typical Laravel app, since Filament does a lot of resource/page/widget
# discovery on every request without them. Migrations still run on every
# boot since Render doesn't give us a separate one-shot "release phase"
# step the way the docker-compose `migrate` service did locally — this is
# the pragmatic equivalent for a single-container platform.
CMD ["sh", "-c", "php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8000"]
