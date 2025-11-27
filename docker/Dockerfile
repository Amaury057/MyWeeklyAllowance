FROM php:8.3-apache

# Copier la configuration vhost qui remplace celle par défaut
COPY vhost.conf /etc/apache2/sites-available/000-default.conf

# Activer le module de réécriture d'URL d'Apache
RUN a2enmod rewrite

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo pdo_mysql zip

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

ENV XDEBUG_MODE=coverage

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
