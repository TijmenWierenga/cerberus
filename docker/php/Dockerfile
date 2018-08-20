FROM php:7.2
LABEL maintainer="t.wierenga@live.nl"

ARG USER_ID=502

RUN usermod -u ${USER_ID} www-data

RUN apt-get update \
    && apt-get install -y \
        zlib1g-dev \
    && docker-php-ext-install zip

RUN pecl install mongodb \
    && docker-php-ext-enable mongodb

COPY --from=composer:latest --chown=www-data /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

RUN chown -R  www-data /var/www

COPY --chown=www-data bin ./bin
COPY --chown=www-data config ./config
COPY --chown=www-data public ./public
COPY --chown=www-data src ./src
COPY --chown=www-data translations ./translations
COPY --chown=www-data tests ./tests
COPY --chown=www-data composer.json composer.lock symfony.lock phpunit.xml.dist ./

ENV MONGODB_URL=mongodb://mongo:27017
ENV MONGODB_DB=oauth

USER www-data
RUN APP_ENV=dev composer install --no-scripts

CMD ["php", "-S", "0.0.0.0:8080", "-t", "/var/www/html/public"]