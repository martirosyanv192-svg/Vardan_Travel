FROM php:8.2-apache

# Անջատում ենք մոդուլները և ջնջում ավելորդ MPM ֆայլերը, որպեսզի կոնֆլիկտ չառաջանա
RUN a2dismod mpm_event mpm_worker mpm_prefork || true
RUN rm -f /etc/apache2/mods-enabled/mpm_*.load /etc/apache2/mods-enabled/mpm_*.conf
RUN a2enmod mpm_prefork

RUN docker-php-ext-install pdo pdo_mysql mysqli
RUN docker-php-ext-enable pdo_mysql mysqli

RUN a2enmod rewrite

RUN sed -i "s/80/\${PORT}/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf
EXPOSE ${PORT}