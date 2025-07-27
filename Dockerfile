FROM php:8.2.11-fpm

# Install Composer
RUN echo "Install COMPOSER" \
  && curl -sS https://getcomposer.org/installer -o composer-setup.php \
  && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
  && rm composer-setup.php

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Install utilities and libraries
RUN apt-get update && apt-get -y install --fix-missing \
  nano \
  wget \
  vim \
  git \
  curl \
  zip \
  build-essential \
  libcurl4 \
  libcurl4-openssl-dev \
  zlib1g-dev \
  libzip-dev \
  libbz2-dev \
  locales \
  libicu-dev \
  libonig-dev \
  libxml2-dev \
  apt-utils \
  dialog \
  && apt-get clean && rm -rf /var/lib/apt/lists/*

# Set working directory
WORKDIR /var/www

# Expose port for PHP-FPM
EXPOSE 9000

# Run PHP-FPM
CMD ["php-fpm"]
