# Function to update PHP-FPM configuration with the specified port
function update_php_fpm_port {
    local port="$1"
    echo "Updating PHP-FPM port to ${port}"
    sed -i "s/^listen = 0.0.0.0:.*$/listen = 0.0.0.0:${port}/" /usr/local/etc/php-fpm.d/zz-docker.conf
}

# Function to restart PHP-FPM
function restart_php_fpm {
    echo "Restarting PHP-FPM..."
    php-fpm -R
}

# Check if the "PORT" environment variable is set
if [[ -z "${PORT}" ]]; then
    echo "Error: Environment variable PORT is not set. Exiting."
    exit 1
fi

# Call the function to update PHP-FPM port with the specified value
update_php_fpm_port "${PORT}"

# Call the function to restart PHP-FPM
restart_php_fpm
