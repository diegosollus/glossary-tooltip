FROM php:8.1.11-fpm

RUN apt-get update && apt-get install -y \
    zip \
    unzip \
    apt-utils \
    npm

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions \
        bcmath \
        exif \
        gd \
        imagick \
        intl \
        gettext \
        memcache \
        memcached \
        opcache \
        pdo_pgsql \
        pgsql \
        soap \
        xdebug \
        zip

# Download and install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Clean repository
RUN apt-get clean
RUN rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*
