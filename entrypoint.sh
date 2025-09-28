#!/bin/sh
set -e

# Ensure writable directories exist
mkdir -p /var/www/html/var/log \
         /var/www/html/public/fileadmin \
         /var/www/html/public/typo3temp

# Fix ownership and permissions
chown -R www-data:www-data /var/www/html
chmod -R 755 /var/www/html
chmod -R 775 /var/www/html/var \
             /var/www/html/public/fileadmin \
             /var/www/html/public/typo3temp

# Run TYPO3 cache commands
su -s /bin/sh www-data -c "vendor/bin/typo3 cache:flush --force || true"
su -s /bin/sh www-data -c "vendor/bin/typo3 cache:warmup || true"

# Start Apache
exec apache2-foreground
