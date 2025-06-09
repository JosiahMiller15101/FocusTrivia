FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev zip curl libpng-dev \
    && docker-php-ext-install pdo pdo_mysql zip gd

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory (important: move this up!)
WORKDIR /var/www/html

# Copy app files (move before composer install)
COPY . .

# Copy Composer from official image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Fix permissions (optional, but good)
RUN chown -R www-data:www-data storage bootstrap/cache

# Expose port 80 and start Apache
EXPOSE 80
CMD ["apache2-foreground"]
