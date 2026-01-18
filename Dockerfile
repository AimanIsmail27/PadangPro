FROM php:8.2-cli

# System deps
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl

# Enable GD
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working dir
WORKDIR /app

# Copy files
COPY . .

# Install deps
RUN composer install --no-dev --optimize-autoloader

# Expose port
EXPOSE 8080

CMD php artisan serve --host=0.0.0.0 --port=8080
