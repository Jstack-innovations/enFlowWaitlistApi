FROM php:8.2-apache

# Debug: print all enabled mods at build time
RUN echo "=== MODS ENABLED ===" && ls /etc/apache2/mods-enabled/

# Install mysqli extension
RUN docker-php-ext-install mysqli pdo pdo_mysql

COPY . /var/www/html/

RUN a2enmod rewrite

EXPOSE 80
