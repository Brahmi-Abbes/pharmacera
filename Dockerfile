FROM php:8.5-fpm

# Install system dependencies PHP needs to talk to MySQL/Postgres, handle images, etc.
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    libpq-dev

# Install the actual PHP extensions your app uses
# pdo_pgsql is the one that was missing — Render's managed database is
# Postgres (no managed MySQL option), so without this driver Laravel can't
# connect at all, which is exactly why migrations were exiting with status 1
RUN docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip intl

# Composer (PHP's package manager) — copied in from its own official image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy your project files into the container
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Laravel needs these folders to be writable
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 8000

CMD ["sh", "-c", "php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8000"]