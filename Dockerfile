# Gunakan PHP dengan Apache
FROM php:8.2-apache

# Install ekstensi yang diperlukan Laravel
RUN apt-get update && apt-get install -y \
    git curl zip unzip libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# Copy Composer dari image resmi
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Salin semua file proyek ke dalam container
COPY . /var/www/html

WORKDIR /var/www/html

# Install dependensi Laravel
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Aktifkan mod_rewrite Apache agar Laravel route bisa jalan
RUN a2enmod rewrite

# Set Apache agar baca file .htaccess
COPY Docker/apache.conf /etc/apache2/sites-available/000-default.conf
