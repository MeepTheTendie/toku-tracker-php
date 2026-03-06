FROM php:8.3-apache

# Enable required extensions
RUN docker-php-ext-install pdo pdo_sqlite

# Copy app files
COPY . /var/www/html/

# Create data directories and set permissions
RUN mkdir -p /var/www/html/data /var/www/html/cache && \
    chown -R www-data:www-data /var/www/html

# Enable mod_rewrite for .htaccess
RUN a2enmod rewrite

# Configure Apache to use .htaccess
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

EXPOSE 80
