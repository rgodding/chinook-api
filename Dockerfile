# Use official PHP image with Apache
FROM php:8.2-apache

# Installer pdo_mysql extension
RUN docker-php-ext-install pdo_mysql

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