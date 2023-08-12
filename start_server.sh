#!/bin/sh

# Function to update PHP-FPM configuration with the specified port
function update_php_fpm_port {
    local port="$1"
    echo "Updating PHP-FPM port to ${port}"
    cat > /usr/local/etc/php-fpm.d/zz-docker.conf << EOF
[www]
listen = 0.0.0.0:${port}
EOF
}

# Function to restart PHP-FPM
function restart_php_fpm {
    local port="$1"
    echo "Restarting PHP-FPM..."
    php-fpm -R
}

# Set default port if "PORT" environment variable is not set
DEFAULT_PORT=9090
PORT=${PORT:-$DEFAULT_PORT}

# Stop php-fpm
kill -9 $(pgrep php-fpm)

# Call the function to update PHP-FPM port with the specified value
update_php_fpm_port "${PORT}"

# Call the function to restart PHP-FPM
restart_php_fpm "${PORT}"

sleep 1
