FROM php:8.0-fpm

# Set working directory
WORKDIR /var/www/ws

# Install dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    build-essential \
    libxml2-dev \
    libldap2-dev \
    libpq-dev \
    locales \
    vim \
    unzip \
    git \
    curl

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install extensions
RUN docker-php-ext-install pdo_pgsql sockets

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

EXPOSE 6001

CMD ["php", "artisan", "websockets:serve"]