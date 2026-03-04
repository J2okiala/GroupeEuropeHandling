FROM php:8.2-cli

WORKDIR /app

# 🔥 Définir l'environnement AVANT toute chose
ENV APP_ENV=prod
ENV APP_DEBUG=0

# Installer extensions nécessaires
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpng-dev libonig-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# Installer MongoDB
RUN pecl install mongodb-1.20.1 \
    && docker-php-ext-enable mongodb

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copier projet
COPY . .

# Installer dépendances en mode production IMPORTANT : désactiver scripts Symfony
RUN composer install --no-interaction --optimize-autoloader --no-dev --no-scripts

EXPOSE 8080

CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]
