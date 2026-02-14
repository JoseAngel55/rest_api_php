FROM php:8.2-apache

# Activar mod_rewrite
RUN a2enmod rewrite

# Permitir que Apache lea .htaccess
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Instalar extensiones MySQL
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Cambiar DocumentRoot a /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

WORKDIR /var/www/html
