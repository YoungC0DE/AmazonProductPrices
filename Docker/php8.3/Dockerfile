FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \ 
    vim \
    zip

RUN docker-php-ext-install zip

RUN pecl install mongodb

RUN docker-php-ext-enable mongodb zip 

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html/amazon-product-prices
