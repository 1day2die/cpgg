# Use the official PHP image as the base image
FROM php:8.2-fpm

ENV COMPOSER_ALLOW_SUPERUSER=1

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    nginx

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl mysqli

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Set working directory
WORKDIR /var/www/html

# Copy existing application directory contents
COPY . /var/www/html

RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 storage/* bootstrap/cache/

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Switch to the www-data user
USER www-data

# Install PHP dependencies
RUN composer install --no-interaction --no-dev --optimize-autoloader

# Switch to the root user
USER root

# Copy the Nginx configuration file
COPY ./nginx/default.conf /etc/nginx/conf.d/default.conf

# Expose port 8000 and start php-fpm server
EXPOSE 8000

CMD service nginx start && php-fpm