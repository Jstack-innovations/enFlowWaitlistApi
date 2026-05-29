FROM php:8.2-apache

# Aggressively fix MPM conflict
RUN cd /etc/apache2/mods-enabled && \
    rm -f mpm_prefork.conf mpm_prefork.load \
          mpm_worker.conf mpm_worker.load \
          mpm_event.conf mpm_event.load && \
    ln -s ../mods-available/mpm_event.conf mpm_event.conf && \
    ln -s ../mods-available/mpm_event.load mpm_event.load

# Install mysqli extension
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy all API files into the container
COPY . /var/www/html/

# Allow .htaccess
RUN a2enmod rewrite

EXPOSE 80
