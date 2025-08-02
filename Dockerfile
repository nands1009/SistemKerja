FROM php:8.1-apache

# Install minimal extensions
RUN docker-php-ext-install mysqli

# Copy semua file
COPY . /var/www/html/

# Set Apache document root ke public CI4
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Enable rewrite module
RUN a2enmod rewrite

# Set permissions
RUN chmod -R 755 /var/www/html

# Railway needs this for port
CMD ["apache2-foreground"]
