FROM php:7.4-fpm

# Install dependencies and required libraries
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg-dev \
    libpng-dev \
    libicu-dev \
    libonig-dev \
    libmcrypt-dev \
    --no-install-recommends

# Install PHP extensions
RUN docker-php-ext-install intl mbstring pdo_mysql exif && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd

# Install mcrypt via PECL and enable it
RUN pecl install mcrypt-1.0.4 && \
    docker-php-ext-enable mcrypt

# Install xdebug for PHP 7.4 specifically
RUN pecl install xdebug-2.9.8 && \
    docker-php-ext-enable xdebug

# Clean up
RUN rm -rf /var/lib/apt/lists/*

# Copy configuration files
COPY ./php.ini /usr/local/etc/php/
COPY ./www.conf /usr/local/etc/php-fpm.d/

# Clean up unnecessary packages
RUN apt-get purge -y g++ && \
    apt-get autoremove -y

# Adjust user permissions
RUN usermod -u 1000 www-data

EXPOSE 9000
CMD ["php-fpm"]

