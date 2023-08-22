#! /bin/bash

# Function to update nginx configuration with the specified port
function update_nginx_port {
    local port="$1"
    echo "Updating nginx port to ${port}"
    awk -v port="$PORT" '{gsub(/\$\{PORT\}/, port)}1' /etc/nginx/nginx.conf > /etc/nginx/nginx.conf.tmp && mv /etc/nginx/nginx.conf.tmp /etc/nginx/nginx.conf
}

# Function to restart PHP-FPM
function restart_php_fpm {
    local port="$1"
    echo "Restarting PHP-FPM..."
    cd /var/www/html
    cp .env.example .env
    nginx
    php-fpm
}

# Set default port if "PORT" environment variable is not set
DEFAULT_PORT=9090
PORT=${PORT:-$DEFAULT_PORT}

# Stop php-fpm
kill -9 $(pgrep php-fpm)

# Call the function to update nginx port with the specified value
update_nginx_port "${PORT}"

# Call the function to restart PHP-FPM
restart_php_fpm "${PORT}"

sleep 1
