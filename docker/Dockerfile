FROM php:8.1-fpm

WORKDIR /var/www/html

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN docker-php-ext-install pdo pdo_mysql
RUN apt-get update && \
    apt-get install -y \
        sendmail \
        zlib1g-dev \
        libonig-dev \
        libzip-dev \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        ssh \
        rsync \
        unzip

# Install unzip utility and libs needed by zip PHP extension
RUN docker-php-ext-install zip

RUN apt install -y git

RUN docker-php-ext-install exif

RUN docker-php-ext-install mbstring

RUN docker-php-ext-configure gd --with-freetype --with-jpeg

RUN docker-php-ext-install gd

RUN pecl install xdebug && docker-php-ext-enable xdebug
RUN echo zend_extension = "xdebug.so"
RUN echo 'xdebug.mode=debug' >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo 'xdebug.client_port=9002' >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
