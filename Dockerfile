# Use PHP 8.2 with Apache
FROM php:8.2-apache

# Install PHP extensions for TYPO3
RUN apt-get update && apt-get install -y \
    git unzip libpng-dev libjpeg-dev libwebp-dev libfreetype6-dev libxml2-dev libicu-dev libzip-dev \
    nodejs npm \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) gd intl pdo_mysql zip opcache \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

# Set working directory
WORKDIR /var/www/html

# Copy TYPO3 project
COPY . /var/www/html

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Install Node dependencies and build frontend
RUN npm install \
    && npm run build

# Expose Apache port
EXPOSE 80
