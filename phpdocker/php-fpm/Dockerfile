FROM php:8.1.6-fpm
WORKDIR "/symfogram"

RUN apt-get update; \
    apt-get -y --no-install-recommends install \
        git \
    apt-get clean; \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

RUN apt-get update && \
    apt-get install -y libpq-dev && \
    docker-php-ext-install pgsql pdo pdo_pgsql

RUN pecl install xdebug
RUN docker-php-ext-enable xdebug

RUN apt-get install -y \
    git \
    zip \
    curl \
    sudo \
    nano \
    unzip \
    libicu-dev \
    libbz2-dev \
    libpng-dev \
    libjpeg-dev \
    libmcrypt-dev \
    libreadline-dev \
    libfreetype6-dev \
    g++

RUN docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
    && docker-php-ext-install gd