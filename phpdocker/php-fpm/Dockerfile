FROM php:8.1.6-fpm
WORKDIR "/symfogram"

RUN apt-get update; \
    apt-get -y --no-install-recommends install \
        git \
    apt-get clean; \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

RUN apt-get update && \
    apt-get install -y libpq-dev && \
    docker-php-ext-install pdo pdo_pgsql pgsql

#RUN  docker-php-ext-install xdebug \
#     && docker-php-ext-enable xdebug \

