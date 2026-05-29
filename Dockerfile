FROM php:8.2-apache

# Fix: disable conflicting MPMs, enable only prefork (required for mod_php)
RUN a2dismod mpm_event mpm_worker 2>/dev/null || true && \
    a2enmod mpm_prefork

# Install mysqli extension
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy all API files into the container
COPY . /var/www/html/

# Allow .htaccess
RUN a2enmod rewrite

EXPOSE 80
