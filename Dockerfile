FROM php:8.3-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends libzip-dev unzip git openssl \
    && docker-php-ext-install pdo_mysql opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV APP_ENV=prod
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY docker/vhost.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite headers && a2dissite 000-default && a2ensite 000-default

COPY . .

RUN composer install --no-dev --no-interaction --no-progress --prefer-dist --optimize-autoloader \
    && mkdir -p var/cache var/log public/uploads \
    && php bin/console asset-map:compile \
    && chown -R www-data:www-data var public/uploads

EXPOSE 80
CMD ["apache2-foreground"]