#!/bin/sh
set -e

# Fallback PORT to 80 if not set (useful for local testing)
export PORT=${PORT:-80}

# Idempotently update Apache configuration to listen on the dynamic port
echo "Listen ${PORT}" > /etc/apache2/ports.conf

# Ensure storage directory exists
mkdir -p /var/www/html/storage/framework/cache/data
mkdir -p /var/www/html/storage/framework/app
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/logs

# Create symbolic link for public storage if it doesn't exist
if [ ! -L /var/www/html/public/storage ]; then
    echo "Creating storage link..."
    php artisan storage:link --no-interaction
fi

# Run migrations if enabled (default is true)
if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    echo "Running database migrations..."
    php artisan migrate --force --no-interaction
fi

# Cache config, routes, and views for performance in production
echo "Caching Laravel configuration, routes, and views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# =================================================================
# KUNCI UTAMA: Reset ulang semua kepemilikan file ke www-data (Apache)
# Karena perintah php artisan di atas dijalankan sebagai 'root',
# semua file cache yang baru jadi wajib kita serahkan haknya ke Apache.
# =================================================================
echo "Fixing file permissions for Apache (www-data)..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Execute the main container command (configured as Apache)
echo "Starting web server on port ${PORT}..."
exec "$@"