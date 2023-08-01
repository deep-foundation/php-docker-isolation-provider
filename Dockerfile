# Use a base PHP image
FROM php:8.1-fpm-alpine

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod uga+x /usr/local/bin/install-php-extensions && sync
# Install dependencies
RUN install-php-extensions \
bcmath \
pdo_mysql \
pdo_pgsql \
gd \
exif \
redis \
pcntl \
zip \
mbstring

# Install dependencies
COPY composer.json composer.lock /var/www/html/
WORKDIR /var/www/html
RUN apk update && \
    apk add --no-cache git && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    composer install --no-scripts --no-autoloader

# Copy the source code into the container
COPY . /var/www/html
# Copy the custom www.conf into the image
RUN rm -f /usr/local/etc/php-fpm.d/*

# Set environment variables
ENV GQL_URN="localhost:3006/gql"
ENV GQL_SSL=0

# Set default port 9090, which will be overridden if "PORT" environment variable is provided
ENV PORT=9090

COPY docker.conf /usr/local/etc/php-fpm.d/docker.conf

# Create a custom www.conf file with the dynamic PHP-FPM port
RUN { \
    echo "[www]"; \
    echo "listen = 0.0.0.0:${PORT}"; \
} > /usr/local/etc/php-fpm.d/zz-docker.conf

CMD ["php-fpm"]
