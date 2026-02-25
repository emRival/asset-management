FROM php:8.3-fpm
RUN apt-get update && apt-get install -y libicu-dev pkg-config
RUN docker-php-ext-install intl
