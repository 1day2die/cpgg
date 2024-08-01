FROM php:8.2-fpm

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
    libzip-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN apt-get -y install php8.2 php8.2-{cli,gd,mysql,pdo,mbstring,tokenizer,bcmath,xml,fpm,curl,zip}

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl 

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Set working directory
WORKDIR /var/www/html

# Copy existing application directory contents
COPY . /var/www/html

RUN chown -R www-data:www-data /var/www/html

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install --no-interaction

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]