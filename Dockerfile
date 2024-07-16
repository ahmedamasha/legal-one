# Use PHP 8.2 FPM base image
FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    git \
    librabbitmq-dev \
    && pecl install amqp \
    && docker-php-ext-enable amqp \
    && docker-php-ext-install pdo_mysql zip sockets

# Install Xdebug (optional, remove if not needed)
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY app/ /var/www/html

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set Composer environment variables
ENV COMPOSER_ALLOW_SUPERUSER=1

# Install dependencies using Composer
RUN composer install

# Expose port 9000 (if needed for debugging)
EXPOSE 9000
