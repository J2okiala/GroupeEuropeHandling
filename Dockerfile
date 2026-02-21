FROM php:8.3-fpm

# Installer dépendances système
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    zip \
    curl \
    nginx

# Installer extensions PHP
RUN docker-php-ext-install pdo pdo_mysql zip

# Installer MongoDB extension version compatible
RUN pecl install mongodb-1.20.0 \
    && docker-php-ext-enable mongodb

# Copier composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copier projet
WORKDIR /var/www/html
COPY . .

# Installer dépendances sans scripts
RUN composer install --no-interaction --optimize-autoloader --no-scripts

# Config Nginx
COPY nginx.conf /etc/nginx/sites-available/default

EXPOSE 80

CMD service nginx start && php-fpm
