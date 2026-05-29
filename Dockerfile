FROM php:8.1-apache

RUN a2dismod mpm_event 2>/dev/null || true

RUN docker-php-ext-install mysqli pdo pdo_mysql

COPY . /var/www/html/

RUN a2enmod rewrite

# Override the default Apache config to enforce mpm_prefork at runtime
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

EXPOSE 80

CMD ["/bin/bash", "-c", "a2dismod mpm_event mpm_worker 2>/dev/null; a2enmod mpm_prefork 2>/dev/null; apache2-foreground"]
