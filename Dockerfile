# Gunakan PHP + Apache
FROM php:8.2-apache

# Install dependencies
RUN apt-get update && apt-get install -y \
    git unzip curl libpng-dev libonig-dev libxml2-dev zip \
    libzip-dev && docker-php-ext-install pdo pdo_mysql zip

# Enable mod_rewrite Apache
RUN a2enmod rewrite

# Copy file Laravel ke container
COPY . /var/www/html

# Set working directory
WORKDIR /var/www/html

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install dependensi Laravel
RUN composer install --no-dev --optimize-autoloader

# Set permission
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage

# Copy konfigurasi Apache
COPY ./docker/apache.conf /etc/apache2/sites-available/000-default.conf
