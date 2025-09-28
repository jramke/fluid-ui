# Use PHP 8.2 with Apache
FROM php:8.2-apache

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    unzip \
    libpng-dev libjpeg-dev libwebp-dev libfreetype6-dev \
    libxml2-dev libicu-dev libzip-dev \
    curl \
    nodejs npm \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) gd intl pdo_mysql mysqli zip opcache \
    && a2enmod rewrite \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# PHP settings
RUN { \
    echo 'memory_limit = 256M'; \
    echo 'max_execution_time = 240'; \
    echo 'max_input_vars = 1500'; \
    echo 'upload_max_filesize = 32M'; \
    echo 'post_max_size = 32M'; \
} > /usr/local/etc/php/conf.d/typo3.ini

# Configure Apache document root
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf \
    && echo '<Directory /var/www/html/public>\n\
    AllowOverride All\n\
    Require all granted\n\
    </Directory>' >> /etc/apache2/apache2.conf

# Working directory
WORKDIR /var/www/html

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy files
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-scripts \
    && composer run-script post-install-cmd || true

# Build frontend assets if needed
RUN if [ -f "package.json" ]; then \
        npm i && \
        npm run docs:build; \
    fi

# Copy entrypoint
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Expose port
EXPOSE 80

HEALTHCHECK --interval=10s --timeout=5s --retries=5 \
  CMD curl -s -o /dev/null http://localhost/index.php || exit 1

# Use custom entrypoint
ENTRYPOINT ["/entrypoint.sh"]
