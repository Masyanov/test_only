FROM php:8.3-fpm

# Install system dependencies (опционально, если нужна компиляция)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql

WORKDIR /var/www
