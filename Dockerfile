# Use official PHP image with Apache
FROM php:8.2-apache

# Installer nødvendige pakker til PostgreSQL og pdo_mysql og pdo_pgsql extensions
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo_mysql pdo_pgsql

# Enable mod_rewrite for Apache (common for routing)
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy all project files into the container
COPY . /var/www/html/

# Set permissions if needed
RUN chown -R www-data:www-data /var/www/html

# Expose port 80
EXPOSE 80
