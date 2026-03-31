FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    unzip \
    libsqlite3-dev \
    sqlite3 \
    && docker-php-ext-install pdo pdo_sqlite \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

COPY . /app

RUN mkdir -p /app/data /app/storage_private \
    && chmod -R 775 /app/data /app/storage_private

CMD sh -c "php -S 0.0.0.0:${PORT:-8080} -t /app"
