FROM lagdo/alpine-php-nginx-unit

ARG PHP_USER
ARG PHP_UID
ARG PHP_GID
ENV PHP_USER=${PHP_USER}
ENV PHP_UID=${PHP_UID}
ENV PHP_GID=${PHP_GID}

RUN set -xe && \
  apk add --no-cache bash pcre curl wget zip vim git && \
  apk add --no-cache --virtual .build-deps \
  libzip-dev freetype-dev icu-dev libmcrypt-dev libxslt-dev patch openssh-client

RUN --mount=type=bind,from=mlocati/php-extension-installer:2,source=/usr/bin/install-php-extensions,target=/usr/local/bin/install-php-extensions \
  install-php-extensions gmp intl sockets phar gd mbstring redis yaml \
  pdo pdo_sqlite sqlite3 pdo_mysql mysqlnd mysqli pdo_pgsql pgsql

# PHP Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Configure Nginx Unit
COPY tontine/config/www.json /docker-entrypoint.d/config.json

RUN addgroup -g ${PHP_GID} --system ${PHP_USER}
RUN adduser -G ${PHP_USER} --system -D -s /bin/sh -u ${PHP_UID} ${PHP_USER}

# Create project root
RUN mkdir -p /var/www

# Make sure files/folders needed by the processes are accessible
RUN chown -R ${PHP_USER}:${PHP_USER} /var/www /var/run /run

WORKDIR /var/www
