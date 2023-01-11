FROM php:8-alpine

# Set working directory
WORKDIR /var/www/cron

COPY /crontab /etc/crontabs/root

# Install extensions
RUN docker-php-ext-install pdo pdo_mysql

CMD ["crond", "-f"]