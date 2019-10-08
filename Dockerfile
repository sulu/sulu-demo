# install php dependencies in intermediate container
FROM composer:latest AS composer

WORKDIR /var/www/html

COPY composer.* /var/www/html/

RUN composer global require hirak/prestissimo --no-plugins --no-scripts
RUN composer install --apcu-autoloader -o --no-dev --no-scripts --ignore-platform-reqs

# install admin javascript dependencies and build assets in intermediate container
FROM node:12 AS node-admin

COPY composer.json /var/www/html/
COPY assets/admin /var/www/html/assets/admin
COPY --from=composer /var/www/html/vendor/sulu/sulu /var/www/html/vendor/sulu/sulu
COPY --from=composer /var/www/html/vendor/friendsofsymfony/jsrouting-bundle /var/www/html/vendor/friendsofsymfony/jsrouting-bundle

RUN cd /var/www/html/assets/admin && npm ci && NODE_OPTIONS="--max_old_space_size=4096" npm run build

# install website javascript dependencies and build assets in intermediate container
FROM node:12 AS node-website

COPY assets/website /var/www/html/assets/website
COPY public/website /var/www/html/public/website

RUN cd /var/www/html/assets/website && npm ci && npm run build:css && npm run build:js

# build actual application image
FROM php:7.3-apache AS apache

WORKDIR /var/www/html

# install packages
# inkscape is recommended for handling svg files with imagemagick
RUN apt-get update && apt-get install -y \
        openssl \
        git \
        unzip \
        libicu-dev \
        libmagickwand-dev \
        inkscape

# install PHP extensions
RUN docker-php-ext-configure intl && docker-php-ext-install -j$(nproc) \
        intl \
        pdo \
        pdo_mysql \
        opcache \
        zip

RUN pecl install imagick redis apcu && docker-php-ext-enable imagick redis apcu

# apache config
RUN /usr/sbin/a2enmod rewrite && /usr/sbin/a2enmod headers && /usr/sbin/a2enmod expires

# php config
ADD ./deploy/config/php.ini /usr/local/etc/php/conf.d/custom.ini

# copy needed files from build containers
COPY --from=node-admin /var/www/html/public/build/admin/ /var/www/html/public/build/admin/
COPY --from=node-website /var/www/html/public/build/website/ /var/www/html/public/build/website/
COPY --from=composer /var/www/html/vendor/ /var/www/html/vendor/

COPY . /var/www/html/

FROM apache AS prod

# apache config
COPY ./deploy/config/prod.conf /etc/apache2/sites-available/000-default.conf

FROM apache AS stage

# apache config
COPY ./deploy/config/.htpasswd /etc/apache2/.htpasswd
COPY ./deploy/config/stage.conf /etc/apache2/sites-available/000-default.conf