FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    ffmpeg \
    ghostscript \
    imagemagick \
    build-essential \
    libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
    zip unzip git curl vim \
    libonig-dev libxml2-dev libzip-dev \
    pkg-config libssl-dev \
    && docker-php-ext-configure gd \
        --with-jpeg \
        --with-freetype \
    && docker-php-ext-install -j$(nproc) \
        gd pdo_mysql mbstring exif pcntl bcmath zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY install.sh /usr/local/bin/install.sh
RUN chmod +x /usr/local/bin/install.sh

ENTRYPOINT ["install.sh"]
CMD ["php-fpm"]
