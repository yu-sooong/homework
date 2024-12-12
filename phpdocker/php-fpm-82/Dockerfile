FROM php:8.2-fpm
WORKDIR "/var/www/html"

RUN apt-get update; \
    apt-get -y --no-install-recommends install \
        php8.2-xml \
        php8.2-dom \
        php8.2-mysql \
        php8.2-bcmath \
        php8.2-cgi \
        php8.2-gd \
        php8.2-http \
        php8.2-ldap \
        php8.2-memcached \
        php8.2-mysql \
        php8.2-oauth \
        php8.2-odbc \
        php8.2-redis \
        php8.2-ssh2 \
        php-redis \
        php8.2-uuid; \
    apt-get clean; \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

RUN apt update -y
RUN apt install -y git
RUN apt install vim -y 

RUN apt install bash
RUN ln -sf /bin/bash /bin/sh

RUN pecl install redis && docker-php-ext-enable redis
RUN docker-php-ext-install pdo pdo_mysql

RUN apt-get update && apt-get install -y \
    libmagickwand-dev --no-install-recommends \
    && pecl install imagick \
	&& docker-php-ext-enable imagick

RUN apt-get update && apt-get install -y libpng-dev 
RUN apt-get install -y \
    libwebp-dev \
    libjpeg62-turbo-dev \
    libpng-dev libxpm-dev \
    libfreetype6-dev

RUN docker-php-ext-configure gd --with-freetype --with-jpeg

RUN docker-php-ext-install gd

# 安裝 SSL 相關的憑證
RUN \
  apt-get update && \
  apt-get install ca-certificates && \
  apt-get clean

# 安裝 composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# 安裝 xdebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN apt-get update && apt-get install -y \
    zlib1g-dev \
    libzip-dev \
    zip \
    unzip

RUN docker-php-ext-install zip
RUN docker-php-ext-install sockets
