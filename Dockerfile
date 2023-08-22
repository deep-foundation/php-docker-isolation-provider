# Use a base PHP image
FROM php:7.4.33-fpm AS php-image
FROM python:3.10

RUN apt-get update && apt-get install -y \
    nginx \
    git \
    curl \
    php-fpm \
    php

RUN pip3 install aiohttp==3.8.4 \
                 aiosignal==1.3.1 \
                 async-timeout==4.0.2 \
                 backoff==2.2.1 \
                 botocore==1.29.129 \
                 frozenlist==1.3.3 \
                 gql==3.4.1 \
                 graphql-core==3.2.3 \
                 jmespath==1.0.1 \
                 multidict==6.0.4 \
                 websockets==10.4 \
                 deepclient==1.0.1 \
                 yarl==1.9.2

COPY --from=php-image /usr/local/bin/php /usr/local/bin/php
COPY --from=php-image /usr/local/etc/php /usr/local/etc/php
COPY --from=php-image /usr/local/etc/php-fpm.conf /usr/local/etc/php-fpm.conf

COPY --from=php-image /usr/local/include/php /usr/local/include/php
COPY --from=php-image /usr/local/lib/php /usr/local/lib/php
COPY --from=php-image /usr/local/php /usr/local/php
COPY --from=php-image /usr/local/sbin/php-fpm /usr/local/sbin/php-fpm

COPY --from=php-image /usr/lib/x86_64-linux-gnu/libargon2.so.1 \
        /usr/lib/x86_64-linux-gnu/libssl.so.1.1 \
        /usr/lib/x86_64-linux-gnu/libcrypto.so.1.1 \
        /usr/lib/x86_64-linux-gnu/libonig.so.5 \
        /usr/lib/x86_64-linux-gnu/

# Set environment variables
ENV GQL_URN="192.168.0.135:3006/gql"
ENV GQL_SSL=0

WORKDIR /var/www/html

# Copy the source code into the container
COPY . /var/www/html

# Copy the start_server.sh script into the image
# Copy Nginx configuration file
RUN mv /var/www/html/nginx.conf /etc/nginx/nginx.conf \
    && mkdir /usr/local/etc/php-fpm.d \
    && mv /var/www/html/docker.conf /usr/local/etc/php-fpm.d/docker.conf \
    && mv /var/www/html/start_server.sh /usr/local/bin/start_server.sh \
    && mv /var/www/html/compose /usr/local/bin/compose \
    && mv /var/www/html/deep_client_php_extension.so /usr/local/lib/php/extensions/no-debug-non-zts-20190902/deep_client_php_extension.so \
    && chmod +x /usr/local/bin/start_server.sh \
    && chmod +x /usr/local/bin/compose \
    && echo "extension=deep_client_php_extension.so" > /usr/local/etc/php/conf.d/deep_client_php_extension.ini \
    && mkdir -p /var/www/logs && chown -R www-data:www-data /var/www/logs \
    && touch /run/php7.4-fpm.sock \
    && chown www-data:www-data /run/php7.4-fpm.sock \
    && chmod 777 /run/php7.4-fpm.sock

RUN /usr/local/bin/compose install

# Set the script as the entry point for the container
ENTRYPOINT ["/usr/local/bin/start_server.sh"]