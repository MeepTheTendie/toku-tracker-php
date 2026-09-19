FROM php:8.4-apache

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public \
    TOKU_DB=/var/lib/toku-tracker/toku.db \
    TOKU_BASE_PATH= \
    TOKU_HTTPS=0

RUN apt-get update \
    && apt-get install -y --no-install-recommends libsqlite3-dev \
    && docker-php-ext-install pdo_sqlite \
    && rm -rf /var/lib/apt/lists/* \
    && a2enmod rewrite \
    && sed -ri "s!/var/www/html!$APACHE_DOCUMENT_ROOT!g" /etc/apache2/sites-available/*.conf /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

COPY . /var/www/html/

RUN mkdir -p /var/lib/toku-tracker \
    && chown -R www-data:www-data /var/lib/toku-tracker \
    && find /var/www/html -type d -exec chmod 755 {} + \
    && find /var/www/html -type f -exec chmod 644 {} +

VOLUME ["/var/lib/toku-tracker"]
EXPOSE 80
