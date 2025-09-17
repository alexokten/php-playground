FROM dunglas/frankenphp

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install pdo pdo_mysql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy composer.json first
# Copy composer.lock if it exists
COPY composer.json ./
COPY composer.loc[k] ./
RUN composer install --no-dev --optimize-autoloader

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Copy application files
COPY . /app

# Expose port 80 and 443 for HTTP and HTTPS
EXPOSE 80 443

# Start FrankenPHP to serve from public directory
CMD ["frankenphp", "php-server", "--root", "/app/public"]
