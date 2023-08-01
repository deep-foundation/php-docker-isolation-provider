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
# Copy the custom www.conf into the image
COPY zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf

# Set environment variables
ENV GQL_URN="localhost:3006/gql"
ENV GQL_SSL=0

# Set memory limit to 30MB
CMD ["php-fpm", "-F", "-d", "memory_limit=30M"]

# Expose port 9090
EXPOSE 9090
