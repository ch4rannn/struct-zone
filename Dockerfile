FROM php:8.2-apache

# Install dependencies and extensions
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Enable mod_rewrite for URL routing
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/uploads \
    && chmod -R 755 /var/www/html/assets

# Configure Apache DocumentRoot to use public directory if needed, 
# but this app seems to use root. We'll stick to default /var/www/html.

# Expose port 80
EXPOSE 80
