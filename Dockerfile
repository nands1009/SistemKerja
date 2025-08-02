FROM php:8.1-apache

# Install extensions yang dibutuhkan CI4
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Fix Apache ServerName warning
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Enable mod_rewrite untuk CI4
RUN a2enmod rewrite

# Copy aplikasi
COPY . /var/www/html/

# Set document root ke public CI4
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Configure Apache untuk CI4 - Allow .htaccess
RUN echo '<Directory /var/www/html/public>' >> /etc/apache2/apache2.conf \
    && echo '    Options Indexes FollowSymLinks' >> /etc/apache2/apache2.conf \
    && echo '    AllowOverride All' >> /etc/apache2/apache2.conf \
    && echo '    Require all granted' >> /etc/apache2/apache2.conf \
    && echo '</Directory>' >> /etc/apache2/apache2.conf

# Set permissions
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html
RUN chmod -R 777 /var/www/html/writable

# Expose port 80 (Railway akan map ke PORT environment variable)
EXPOSE 80

# Start Apache in foreground
CMD ["apache2-foreground"]
