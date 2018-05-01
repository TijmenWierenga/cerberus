FROM php:7.2
LABEL maintainer="t.wierenga@live.nl"

RUN apt-get update \
    && apt-get install -y \
        zlib1g-dev \
    && docker-php-ext-install zip

RUN pecl install mongodb \
    && docker-php-ext-enable mongodb

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer