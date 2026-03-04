# Utiliser PHP 8.2 CLI
FROM php:8.2-cli

WORKDIR /app

# 🔥 Définir l'environnement AVANT toute chose
ENV APP_ENV=prod
ENV APP_DEBUG=0

# Installer extensions PHP et outils nécessaires
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpng-dev libonig-dev \
    nodejs npm \
    && docker-php-ext-install pdo pdo_mysql zip \
    && pecl install mongodb-1.20.1 \
    && docker-php-ext-enable mongodb \
    && rm -rf /var/lib/apt/lists/*

# Installer Composer depuis l'image officielle
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copier projet
COPY . .

# Installer dépendances PHP en production
RUN composer install --no-interaction --optimize-autoloader --no-dev --no-scripts

# Installer dépendances JS et builder les assets
RUN npm install \
    && npm run build

# Exposer le port
EXPOSE 8080

# Lancer le serveur PHP intégré
CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]
