FROM php:8.2-apache

RUN apt-get update && apt-get install -y libcurl4-openssl-dev \
    && docker-php-ext-install curl mysqli pdo pdo_mysql

RUN a2dismod mpm_event 2>/dev/null || true

COPY . /var/www/html/

RUN a2enmod rewrite
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

EXPOSE 80

CMD ["/bin/bash", "-c", "a2dismod mpm_event mpm_worker 2>/dev/null; a2enmod mpm_prefork 2>/dev/null; apache2-foreground"]
