# PHP extension installer
FROM mlocati/php-extension-installer:2 AS php-extension-installer

FROM php:8.2-fpm-alpine

COPY --from=php-extension-installer ["/usr/bin/install-php-extensions", "/usr/local/bin/install-php-extensions"]

RUN set -xe && \
  install-php-extensions opcache zip intl sockets bcmath ctype \
    phar dom fileinfo gd mbstring openssl redis tokenizer gmp yaml

# PHP Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www
