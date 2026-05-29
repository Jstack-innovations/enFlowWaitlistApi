FROM php:8.2-apache

# Railway fix
RUN a2dismod mpm_event 2>/dev/null || true

# Install mysqli extension
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy all API files into the container
COPY . /var/www/html/

# Allow .htaccess
RUN a2enmod rewrite

EXPOSE 80
