FROM php:8.2-apache

# Nuclear fix: wipe ALL mpm configs and force only prefork
RUN rm -f /etc/apache2/mods-enabled/mpm_*.load \
          /etc/apache2/mods-enabled/mpm_*.conf \
          /etc/apache2/mods-enabled/mpm_*.load \
    && ln -sf /etc/apache2/mods-available/mpm_prefork.load \
              /etc/apache2/mods-enabled/mpm_prefork.load \
    && ln -sf /etc/apache2/mods-available/mpm_prefork.conf \
              /etc/apache2/mods-enabled/mpm_prefork.conf

# Install mysqli extension
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy all API files
COPY . /var/www/html/

# Allow .htaccess
RUN a2enmod rewrite

EXPOSE 80
