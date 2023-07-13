# Base image
FROM php:8.1.6-fpm

# Set working directory
WORKDIR /var/www/html

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libonig-dev \
    libzip-dev \
    zip \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring zip

# Install Composer
COPY --from=composer:2.0 /usr/bin/composer /usr/bin/composer

# Copy project files
COPY . /var/www/html

# Install dependencies
RUN composer install --optimize-autoloader --no-dev --ignore-platform-reqs

# Generate application key
RUN php artisan key:generate

# Set file permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port
EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"]
