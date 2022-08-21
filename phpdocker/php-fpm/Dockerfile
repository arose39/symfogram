FROM php:8.1.6-fpm
WORKDIR "/symfogram"

RUN apt-get update; \
    apt-get -y --no-install-recommends install \
        git \
    apt-get clean; \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

RUN  pecl install xdebug \
     && docker-php-ext-enable xdebug \