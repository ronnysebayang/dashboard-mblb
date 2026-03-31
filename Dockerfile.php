FROM php:8.2-apache

# Install extension yang umum dibutuhkan
RUN apt-get update && apt-get install -y \
    unzip \
    libsqlite3-dev \
    sqlite3 \
    && docker-php-ext-install pdo pdo_sqlite sqlite3 \
    && a2enmod rewrite

# Copy konfigurasi Apache
COPY apache.conf /etc/apache2/sites-available/000-default.conf

# Copy semua file project ke web root
COPY . /var/www/html/

# Atur permission dasar
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && mkdir -p /var/www/html/uploads /var/www/html/data \
    && chmod -R 775 /var/www/html/uploads /var/www/html/data

EXPOSE 10000

# Ubah Apache agar listen ke port Render
RUN sed -i 's/80/10000/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

CMD ["apache2-foreground"]