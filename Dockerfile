FROM php:8.2-apache

RUN docker-php-ext-install mysqli

COPY bacll_system/ /var/www/html/

EXPOSE 8080

CMD ["sh", "-c", "PORT=${PORT:-8080} && rm -f /etc/apache2/mods-enabled/mpm_event.* /etc/apache2/mods-enabled/mpm_worker.* && a2enmod mpm_prefork && sed -i \"s/Listen 80/Listen ${PORT}/\" /etc/apache2/ports.conf && sed -i \"s/<VirtualHost \\*:80>/<VirtualHost *:${PORT}>/\" /etc/apache2/sites-enabled/000-default.conf && apache2-foreground"]
