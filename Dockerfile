# Use a base PHP image
FROM php:8.1-apache

# Install dependencies
COPY composer.json composer.lock /var/www/html/
WORKDIR /var/www/html
RUN apt-get update && \
    apt-get install -y git && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    composer install --no-scripts --no-autoloader

# Copy the source code into the container
COPY . /var/www/html

# Set environment variables
ENV GQL_URN="localhost:3006/gql"
ENV GQL_SSL=0

# Configure Apache
RUN a2enmod rewrite

# Set the port on which the application will run
ENV PORT=80

# Start Apache
CMD ["apache2-foreground"]