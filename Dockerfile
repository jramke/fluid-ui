# Use PHP 8.2 with Apache
FROM php:8.2-apache

# Install only necessary system dependencies and PHP extensions for TYPO3
RUN apt-get update && apt-get install -y \
    unzip \
    libpng-dev libjpeg-dev libwebp-dev libfreetype6-dev \
    libxml2-dev libicu-dev libzip-dev \
    nodejs npm \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) gd intl pdo_mysql mysqli zip opcache \
    && a2enmod rewrite \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Essential PHP settings for TYPO3
RUN { \
    echo 'memory_limit = 256M'; \
    echo 'max_execution_time = 240'; \
    echo 'max_input_vars = 1500'; \
    echo 'upload_max_filesize = 32M'; \
    echo 'post_max_size = 32M'; \
} > /usr/local/etc/php/conf.d/typo3.ini

# Configure Apache document root to point to public directory
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Set working directory
WORKDIR /var/www/html

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy all application files (needed for local path dependencies)
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Run post-install scripts
RUN composer run-script post-install-cmd || true

# Build frontend assets
RUN if [ -f "package.json" ]; then \
        npm i && \
        npm run build; \
    fi

# Set secure permissions
RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && find /var/www/html -type f -exec chmod 644 {} \; \
    && chmod 775 /var/www/html/var /var/www/html/public/fileadmin /var/www/html/public/typo3temp 2>/dev/null || true \
    && chmod +x /var/www/html/vendor/bin/typo3 2>/dev/null || true

# Expose port
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]