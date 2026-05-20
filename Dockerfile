FROM php:8.2-apache

# Install mysqli extension
RUN docker-php-ext-install mysqli

# Copy all API files into the container
COPY . /var/www/html/

# Allow .htaccess
RUN a2enmod rewrite

EXPOSE 80
