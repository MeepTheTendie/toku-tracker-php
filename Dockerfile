FROM php:8.3-apache

# Install required extensions
RUN docker-php-ext-install pdo pdo_sqlite

# Enable mod_rewrite
RUN a2enmod rewrite

# Copy app files
COPY . /var/www/html/

# Set proper permissions for Railway
RUN mkdir -p /var/www/html/data /var/www/html/cache && \
    chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# Configure Apache for Railway's PORT env variable
RUN sed -i 's/Listen 80/Listen ${PORT}/g' /etc/apache2/ports.conf && \
    sed -i 's/:80/:${PORT}/g' /etc/apache2/sites-available/000-default.conf && \
    sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD curl -f http://localhost:${PORT}/ || exit 1

# Railway will set PORT env variable automatically
ENV PORT=80
EXPOSE 80
