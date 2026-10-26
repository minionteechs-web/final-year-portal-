FROM php:8.2-apache

# Enable the rewrite module (needed for pretty URLs)
RUN a2enmod rewrite

# Set the working directory to the portal folder
WORKDIR /var/www/html

# Copy the portal source into the container
COPY portal/ /var/www/html/

# Set permissions so Apache can read/write if needed
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expose the default HTTP port
EXPOSE 80

# Start Apache in the foreground (Vercel expects the process to keep running)
CMD ["apache2-foreground"]