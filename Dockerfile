# Use a base PHP image
FROM php:8.1-fpm-alpine

# Install dependencies
COPY composer.json composer.lock /var/www/html/
WORKDIR /var/www/html
RUN apk update && \
    apk add --no-cache git && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    composer install --no-scripts --no-autoloader

# Copy the source code into the container
COPY . /var/www/html

# Set environment variables
ENV GQL_URN="localhost:3006/gql"
ENV GQL_SSL=0

EXPOSE 9000

# Start PHP-FPM and keep it running with a long-running command
CMD ["php-fpm", "-F"]
