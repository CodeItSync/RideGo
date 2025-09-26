#!/bin/bash
# Set permissions
chmod -R 775 /var/app/current/storage /var/app/current/bootstrap/cache

# Cache configs
cd /var/app/current
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations once
if [ -z "$EB_IS_COMMAND_RUNNER" ]; then
    php artisan migrate --force
fi
