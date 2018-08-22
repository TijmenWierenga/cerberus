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

RUN mkdir -p /var/www/html && chown -R www-data:www-data /var/www

COPY --from=composer:latest --chown=www-data /usr/bin/composer /usr/bin/composer
COPY --chown=www-data composer.json composer.lock /var/www/html/

ENV APP_ENV=prod
ENV MONGODB_URL=mongodb://mongo:27017
ENV MONGODB_DB=oauth
ENV CORS_ALLOW_ORIGIN=^https?://localhost:?[0-9]*$

USER www-data
WORKDIR /var/www/html
RUN composer install --no-autoloader --no-scripts
COPY --chown=www-data . /var/www/html
RUN composer dump-autoload --optimize

CMD ["php", "-S", "0.0.0.0:8080", "-t", "/var/www/html/public"]