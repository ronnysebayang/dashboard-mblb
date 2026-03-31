FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    unzip \
    libsqlite3-dev \
    sqlite3 \
    && docker-php-ext-install pdo pdo_sqlite \
    && a2enmod rewrite

COPY apache.conf /etc/apache2/sites-available/000-default.conf
COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && mkdir -p /var/www/html/data /var/www/html/storage_private \
    && chmod -R 775 /var/www/html/data /var/www/html/storage_private

EXPOSE 80

CMD ["apache2-foreground"]
