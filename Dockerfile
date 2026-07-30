FROM php:8.2-apache

# Միացնում ենք pdo_mysql և mysqli
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Հանում ենք MPM կոնֆլիկտը
RUN a2dismod mpm_event && a2enmod mpm_prefork

# Railway-ի PORT-ը կարգավորում ենք
ENV APACHE_LISTEN_PORT=8080
RUN sed -i 's/80/${APACHE_LISTEN_PORT}/g' /etc/apache2/ports.conf \
    && sed -i 's/:80/:${APACHE_LISTEN_PORT}/g' /etc/apache2/sites-available/000-default.conf

# Կոդը պատճենում ենք
COPY . /var/www/html/

# Թույլտվություններ
RUN chown -R www-data:www-data /var/www/html \
    && a2enmod rewrite headers

EXPOSE 8080

CMD ["apache2-foreground"]