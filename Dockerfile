FROM php:8.2-apache

# Install system dependencies and PHP extensions needed by Laravel
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip git && \
    docker-php-ext-install pdo pdo_mysql zip && \
    a2enmod rewrite

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy project files
COPY . .

# Install Laravel dependencies
RUN rm -rf /var/www/html/index.html && \
    cp -r public/* /var/www/html/ && \
    cp public/index.php /var/www/html/index.php && \
    cp public/.htaccess /var/www/html/.htaccess

# Set permissions for Laravel storage and cache folders
RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 80

CMD ["apache2-foreground"]