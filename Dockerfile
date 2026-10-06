FROM php:8.2-apache

RUN docker-php-ext-install mysqli

COPY bacll_system/ /var/www/html/

EXPOSE 80

CMD ["sh", "-c", "rm -f /etc/apache2/mods-enabled/mpm_event.* /etc/apache2/mods-enabled/mpm_worker.* && a2enmod mpm_prefork && apache2-foreground"]
