FROM php:8.2-cli

# Installer extensions nécessaires
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpng-dev libonig-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# Installer MongoDB extension compatible
RUN pecl install mongodb-1.20.1 \
    && docker-php-ext-enable mongodb

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

RUN composer install --no-interaction --optimize-autoloader --no-dev --no-scripts

ENV APP_ENV=prod
ENV APP_DEBUG=0

EXPOSE 8080

CMD ["sh", "-c", "php -S 0.0.0.0:$PORT -t public"]
