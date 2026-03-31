# PHP extension installer
FROM mlocati/php-extension-installer:2 AS php-extension-installer

FROM php:8.2-fpm-alpine

ARG PHP_USER
ARG PHP_UID
ARG PHP_GID
ENV PHP_USER=${PHP_USER}
ENV PHP_UID=${PHP_UID}
ENV PHP_GID=${PHP_GID}

COPY --from=php-extension-installer ["/usr/bin/install-php-extensions", "/usr/local/bin/install-php-extensions"]

RUN set -xe && \
  install-php-extensions opcache zip intl sockets bcmath ctype \
    phar dom fileinfo gd mbstring openssl redis tokenizer gmp yaml

# PHP Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Create the user and group
RUN addgroup -g ${PHP_GID} --system ${PHP_USER} \
  && adduser -G ${PHP_USER} --system -D -s /bin/sh -u ${PHP_UID} ${PHP_USER}

# Switch to use a non-root user from here on
USER ${PHP_USER}

WORKDIR /var/www
