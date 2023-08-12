#!/bin/sh

# Function to update nginx configuration with the specified port
function update_nginx_port {
    local port="$1"
    echo "Updating nginx port to ${port}"
    sed -i "s/\$PORT/${PORT}/g" /etc/nginx/nginx.conf
}

# Function to restart PHP-FPM
function restart_php_fpm {
    local port="$1"
    echo "Restarting PHP-FPM..."
    php-fpm && nginx -g 'daemon off;'
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
