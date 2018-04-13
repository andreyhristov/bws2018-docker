FROM php:7.2.4-apache

RUN apt-get update && apt-get install -y \
    libicu-dev \
    zlib1g-dev \
    libxml2-dev \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# install apcu and enable it
RUN pecl install apcu \
    && pecl install apcu_bc-1.0.3 \
    && docker-php-ext-enable apcu --ini-name 10-docker-php-ext-apcu.ini \
    && docker-php-ext-enable apc --ini-name 20-docker-php-ext-apc.ini

RUN docker-php-ext-install -j$(nproc) pdo pdo_mysql soap intl zip

COPY app.bws2018.vhost.conf /etc/apache2/sites-available/app.bws2018.conf
RUN a2dissite 000-default && a2ensite app.bws2018 && a2enmod rewrite

WORKDIR /app
