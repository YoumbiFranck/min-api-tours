# Use official PHP image with FPM
FROM php:8.4-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    git \
    unzip \
    libpq-dev \
    icu-dev \
    zip \
    libzip-dev \
    oniguruma-dev

# Install PHP extensions required for Symfony
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    intl \
    opcache \
    zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/symfony

# Copy composer files first for better caching
COPY composer.json composer.lock ./

# Install dependencies
RUN composer install --no-scripts --no-autoloader --prefer-dist

# Copy application files
COPY . .

# Complete Composer installation
RUN composer dump-autoload --optimize

# Create var directory if it doesn't exist and set permissions
RUN mkdir -p /var/www/symfony/var && \
    chown -R www-data:www-data /var/www/symfony/var

# Expose port 9000 for PHP-FPM
EXPOSE 9000

CMD ["php-fpm"]
