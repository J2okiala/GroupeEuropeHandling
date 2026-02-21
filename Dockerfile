FROM php:8.3-apache

# Installer dépendances système
RUN apt-get update && apt-get install -y \
git \
unzip \
libzip-dev \
zip \
curl

# Installer extensions PHP
RUN docker-php-ext-install pdo pdo_mysql zip

# Installer MongoDB extension
RUN pecl install mongodb-1.20.0 \
&& docker-php-ext-enable mongodb

# Activer mod_rewrite
RUN a2enmod rewrite

# Copier le projet
COPY . /var/www/html

WORKDIR /var/www/html

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN composer install --optimize-autoloader --no-interaction --no-scripts

# Config Apache vers public/
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
&& sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
