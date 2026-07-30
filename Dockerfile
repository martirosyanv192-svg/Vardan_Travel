FROM php:8.2-apache

# Միացնում ենք անհրաժեշտ ընդլայնումները
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Apache-ը կարգավորում ենք, որ աշխատի Railway-ի PORT-ով
ENV PORT=8080
RUN sed -i "s/80/\${PORT}/g" /etc/apache2/sites-available/000-default.conf \
    && sed -i "s/80/\${PORT}/g" /etc/apache2/ports.conf

# Կոդը պատճենում ենք
COPY . /var/www/html/

# Թույլտվություններ + rewrite
RUN chown -R www-data:www-data /var/www/html \
    && a2enmod rewrite

# Աշխատեցնում ենք Apache-ը
CMD ["apache2-foreground"]