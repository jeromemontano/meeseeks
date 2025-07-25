FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    git unzip zip curl libicu-dev libonig-dev libxml2-dev libzip-dev libpq-dev \
    && docker-php-ext-install intl pdo pdo_mysql opcache zip

# Install Symfony CLI (Optional but helpful)
RUN curl -sS https://get.symfony.com/cli/installer | bash
ENV PATH="$HOME/.symfony5/bin:$PATH"

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
