FROM php:8.2-apache

# Install MariaDB server and client (allows standalone hosting on Render / Cloud)
RUN apt-get update && apt-get install -y --no-install-recommends \
    mariadb-server \
    mariadb-client \
    && rm -rf /var/lib/apt/lists/*

# Install required PHP extensions for MySQL/MariaDB
RUN docker-php-ext-install mysqli pdo pdo_mysql \
    && docker-php-ext-enable mysqli

# Enable Apache mod_rewrite for clean URLs
RUN a2enmod rewrite

# Set working directory in Apache
WORKDIR /var/www/html

# Copy website files to webroot
COPY . /var/www/html/

# Copy entrypoint script and configure permissions
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 80 10000

ENTRYPOINT ["/entrypoint.sh"]
