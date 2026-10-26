#!/bin/bash
set -e

# Support Render dynamic PORT environment variable (default to 80)
PORT="${PORT:-80}"
sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf
sed -i "s/:80/:${PORT}/g" /etc/apache2/sites-available/000-default.conf

echo "==> Apache configured to listen on port ${PORT}"

# Check if an external database host is provided (Docker Compose: 'db', K8s: 'db-service', Cloud MySQL)
if [ -n "$DB_HOST" ] && [ "$DB_HOST" != "localhost" ] && [ "$DB_HOST" != "127.0.0.1" ]; then
    echo "==> Using external database at ${DB_HOST}:${DB_PORT:-3306}"
else
    echo "==> No external DB_HOST specified. Starting built-in MariaDB service for standalone hosting..."
    service mariadb start || /etc/init.d/mariadb start

    # Wait for MariaDB to become ready
    for i in {1..30}; do
        if mysqladmin ping -h localhost --silent; then
            break
        fi
        sleep 1
    done

    # Initialize database schema and data if not already initialized
    if [ -f "/var/www/html/db/init.sql" ]; then
        echo "==> Initializing parkingdb from init.sql..."
        mysql -u root < /var/www/html/db/init.sql || true
        # Set root password to match default
        mysql -u root -e "ALTER USER 'root'@'localhost' IDENTIFIED VIA mysql_native_password USING PASSWORD('rootpassword'); FLUSH PRIVILEGES;" || true
    fi

    export DB_HOST="127.0.0.1"
    export DB_USER="root"
    export DB_PASSWORD="rootpassword"
    export DB_NAME="parkingdb"
    export DB_PORT="3306"
    echo "==> Local MariaDB is ready."
fi

echo "==> Starting Apache Web Server..."
exec apache2-foreground
