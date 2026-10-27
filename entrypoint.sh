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
    echo "export DB_HOST=\"$DB_HOST\"" >> /etc/apache2/envvars
    echo "export DB_USER=\"${DB_USER:-root}\"" >> /etc/apache2/envvars
    echo "export DB_PASSWORD=\"${DB_PASSWORD:-rootpassword}\"" >> /etc/apache2/envvars
    echo "export DB_NAME=\"${DB_NAME:-parkingdb}\"" >> /etc/apache2/envvars
    echo "export DB_PORT=\"${DB_PORT:-3306}\"" >> /etc/apache2/envvars
else
    echo "==> No external DB_HOST specified. Starting built-in MariaDB service for standalone hosting..."
    
    # Ensure mysql directories and permissions
    mkdir -p /var/lib/mysql /var/run/mysqld
    chown -R mysql:mysql /var/lib/mysql /var/run/mysqld
    chmod 777 /var/run/mysqld

    if [ ! -d "/var/lib/mysql/mysql" ]; then
        echo "==> Initializing MariaDB data directory..."
        mysql_install_db --user=mysql --ldata=/var/lib/mysql > /dev/null 2>&1 || true
    fi

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
        # Set root password
        mysql -u root -e "ALTER USER 'root'@'localhost' IDENTIFIED VIA mysql_native_password USING PASSWORD('rootpassword'); FLUSH PRIVILEGES;" || true
    fi

    export DB_HOST="127.0.0.1"
    export DB_USER="root"
    export DB_PASSWORD="rootpassword"
    export DB_NAME="parkingdb"
    export DB_PORT="3306"

    echo "export DB_HOST=\"127.0.0.1\"" >> /etc/apache2/envvars
    echo "export DB_USER=\"root\"" >> /etc/apache2/envvars
    echo "export DB_PASSWORD=\"rootpassword\"" >> /etc/apache2/envvars
    echo "export DB_NAME=\"parkingdb\"" >> /etc/apache2/envvars
    echo "export DB_PORT=\"3306\"" >> /etc/apache2/envvars

    echo "==> Local MariaDB is ready."
fi

echo "==> Starting Apache Web Server..."
exec apache2-foreground