FROM php:8.2-bookworm

RUN apt-get update && apt-get install -y \
    libzip-dev \
    && docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_mysql \
    zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www
