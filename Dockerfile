FROM php:8.2-apache

WORKDIR /var/www/html

# Install system dependencies including gd and other PHP extensions
RUN apt-get update && apt-get install -y \
    git curl unzip zip python3 gnupg2 libpq-dev libzip-dev libpng-dev libxml2-dev libldap2-dev libonig-dev software-properties-common \
    && docker-php-ext-install pdo pdo_pgsql mbstring xml zip intl ldap gd \
    && a2enmod rewrite \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer (latest stable v2)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy composer files first for caching
COPY composer.json composer.lock /var/www/html/

# Copy full application source
COPY . /var/www/html

# Install PHP dependencies after copying source
RUN composer install --no-interaction --optimize-autoloader

# Set permissions for storage, cache, database (adjust if you use SQLite or other)
RUN chown -R www-data:www-data /var/www/html/database /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/database /var/www/html/storage /var/www/html/bootstrap/cache

# Set Apache DocumentRoot (Laravel's public directory)
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

EXPOSE 80
