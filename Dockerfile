FROM php:7.4-apache

WORKDIR /var/www/html

# Install system dependencies including gd
RUN apt-get update && apt-get install -y \
    git curl unzip zip python3 gnupg2 libpq-dev libzip-dev libpng-dev libxml2-dev libldap2-dev libonig-dev software-properties-common \
    && docker-php-ext-install pdo pdo_pgsql mbstring xml zip intl ldap gd \
    && a2enmod rewrite \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer (latest stable v2)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy composer files first for caching
COPY composer.json composer.lock /var/www/html/

# Copy app source
COPY . /var/www/html

# Set permissions for SQLite, storage, and cache
RUN chown -R www-data:www-data /var/www/html/database /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/database /var/www/html/storage /var/www/html/bootstrap/cache

# Install PHP dependencies
RUN composer install --no-interaction
RUN composer dump-autoload -o

# Set Apache DocumentRoot (Laravel's public)
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

EXPOSE 80
