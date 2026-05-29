FROM php:8.2-apache

# Fix Apache MPM conflict on Railway
RUN a2dismod mpm_prefork mpm_worker mpm_event 2>/dev/null || true \
    && a2enmod mpm_event

# Install mysqli extension
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy all API files into the container
COPY . /var/www/html/

# Allow .htaccess
RUN a2enmod rewrite

EXPOSE 80
