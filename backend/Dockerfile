FROM php:8.1-cli

RUN apt-get update && apt-get install -y \
    curl \
    zip \
    libpq-dev \
    netcat-openbsd \
    && docker-php-ext-install pdo_mysql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

COPY . /app
WORKDIR /app

COPY entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint.sh
ENTRYPOINT ["entrypoint.sh"]