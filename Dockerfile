# ==========================================
# Stage 1: Build Frontend Assets with Node
# ==========================================
FROM node:20-alpine AS node-builder
WORKDIR /app

# Copy dependency files
COPY package.json ./
# Run install
RUN npm install

# Copy configuration and asset source directories
COPY vite.config.js ./
COPY resources/ ./resources/
COPY public/ ./public/

# Compile assets using Vite
RUN npm run build

# ==========================================
# Stage 2: Install Composer Dependencies
# ==========================================
FROM composer:2 AS composer-builder
WORKDIR /app

# Copy composer files
COPY composer.json composer.lock ./

# Install production PHP dependencies
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-scripts \
    --no-interaction \
    --ignore-platform-reqs

# ==========================================
# Stage 3: Production Runtime Environment
# ==========================================
FROM php:8.2-apache AS runtime

# Install system dependencies and PHP development libraries
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libpq-dev \
    libsqlite3-dev \
    zip \
    unzip \
    git \
    && rm -rf /var/lib/apt/lists/*

# Configure and install PHP extensions needed by Laravel
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        gd \
        pdo_mysql \
        pdo_pgsql \
        pdo_sqlite \
        zip \
        opcache \
        bcmath

# Enable Apache mod_rewrite for Laravel routing (.htaccess)
RUN a2enmod rewrite

# Configure PHP OPcache for production performance
RUN { \
    echo 'opcache.enable=1'; \
    echo 'opcache.enable_cli=1'; \
    echo 'opcache.memory_consumption=256'; \
    echo 'opcache.interned_strings_buffer=16'; \
    echo 'opcache.max_accelerated_files=20000'; \
    echo 'opcache.revalidate_freq=0'; \
    echo 'opcache.fast_shutdown=1'; \
} > /usr/local/etc/php/conf.d/opcache-recommended.ini

# Set working directory
WORKDIR /var/www/html

# Copy custom Apache virtual host configuration
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Copy PHP dependencies from Composer builder stage
COPY --from=composer-builder /app/vendor ./vendor

# Copy compiled assets from Node builder stage
COPY --from=node-builder /app/public/build ./public/build

# Copy the rest of the application files
COPY app ./app
COPY bootstrap ./bootstrap
COPY config ./config
COPY database ./database
COPY public ./public
COPY resources ./resources
COPY routes ./routes
COPY artisan ./artisan
COPY composer.json ./composer.json

# Copy and prepare the startup entrypoint script
COPY docker/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Set correct ownership for application directory
RUN chown -R www-data:www-data /var/www/html

# Expose port (Render overrides this with PORT env, but 80 is the default fallback)
EXPOSE 80

# Configure entrypoint and start command
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["apache2-foreground"]
