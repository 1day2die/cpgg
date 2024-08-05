
# Use Ubuntu 22.04 as the base image
FROM ubuntu:22.04

# Avoid user interaction with tzdata, etc.
ENV DEBIAN_FRONTEND=noninteractive
ENV COMPOSER_ALLOW_SUPERUSER=1

RUN mkdir -p /var/www/html
RUN mkdir -p /var/run/php

# Add the PHP PPA from Ondřej Surý
RUN apt-get update && apt-get install -y software-properties-common && \
    add-apt-repository ppa:ondrej/php && \
    apt-get update

# Install system dependencies
RUN apt-get install -y \
    curl git unzip nginx \
    php8.2 php8.2-fpm php8.2-pdo php8.2-mysql php8.2-opcache php8.2-mbstring php8.2-zip \
    php8.2-gd php8.2-intl php8.2-bcmath php8.2-exif php8.2-phar php8.2-xml php8.2-xmlwriter \
    php8.2-curl php8.2-fileinfo php8.2-tokenizer php8.2-redis php8.2-dom php8.2-iconv php8.2-simplexml && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set the working directory
WORKDIR /var/www/html

# Copy the application files to the container
COPY . /var/www/html

# Copy the Nginx configuration file
COPY ./docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# Set permissions and ownership for the application files
RUN chown -R www-data:www-data /var/www/html

# Install PHP dependencies using Composer
RUN composer install --no-interaction --no-dev

# Expose the port Nginx is listening on
EXPOSE 8000

# Start Nginx and PHP-FPM
CMD ["sh", "-c", "php-fpm8.2 -D && nginx -g 'daemon off;'"]