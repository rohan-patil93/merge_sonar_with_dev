FROM php:8.5.13-apache

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev \
    libicu-dev \
    && docker-php-ext-install intl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip \
    && docker-php-ext-install mysqli pdo pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

# Composer binary
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY ./apache2/infi.conf /etc/apache2/conf-available/infi.conf

RUN a2enconf infi.conf \
    && a2enmod rewrite \
    && a2enmod ssl \
    && rm -f /var/www/html/index.html

COPY --chown=www-data:www-data . /var/www/html/

# Install packages from composer.lock (creates vendor/)
RUN cd /var/www/html && composer install --no-dev --optimize-autoloader --no-interaction \
    && chown -R www-data:www-data /var/www/html/vendor

EXPOSE 80 443

CMD ["apache2-foreground"]
