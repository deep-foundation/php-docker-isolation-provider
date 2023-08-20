# Use a base PHP image
FROM python:3.10

# Install additional dependencies
RUN apt-get update && apt-get install -y \
    nginx \
    php7.4 \
    php7.4-cli \
    php7.4-curl


RUN pip3 install deepclient==1.0.1

# Set environment variables
ENV GQL_URN="localhost:3006/gql"
ENV GQL_SSL=0

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
RUN apk update && \
    apk add --no-cache git && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

# Copy the source code into the container
COPY . /var/www/html

RUN composer install

# Copy the custom www.conf into the image
RUN rm -f /usr/local/etc/php-fpm.d/*

# Copy the start_server.sh script into the image
# Copy Nginx configuration file
RUN mv /var/www/html/nginx.conf /etc/nginx/nginx.conf \
    && mv /var/www/html/docker.conf /usr/local/etc/php-fpm.d/docker.conf \
    && mv /var/www/html/start_server.sh /usr/local/bin/start_server.sh \
    && mv /var/www/html/deep_client_php_extension.so /usr/local/lib/php/extensions/no-debug-non-zts-20190902/deep_client_php_extension.so

# Make the script executable
RUN chmod +x /usr/local/bin/start_server.sh

RUN echo "extension=deep_client_php_extension.so" > /usr/local/etc/php/conf.d/deep_client_php_extension.ini

# Create a directory for logs and set ownership
RUN mkdir -p /var/www/logs && chown -R www-data:www-data /var/www/logs

# Set the script as the entry point for the container
ENTRYPOINT ["/usr/local/bin/start_server.sh"]
