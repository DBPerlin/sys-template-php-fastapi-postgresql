FROM php:7.4-apache

ENV TZ=America/Sao_Paulo

RUN apt-get update && apt-get install -y --no-install-recommends \
        libcurl4-openssl-dev \
        libssl-dev \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        libzip-dev \
        ca-certificates \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) curl gd zip opcache \
    && a2enmod proxy proxy_http rewrite headers \
    && rm -rf /var/lib/apt/lists/*

RUN { \
        echo 'upload_max_filesize=20M'; \
        echo 'post_max_size=25M'; \
        echo 'memory_limit=256M'; \
        echo 'max_execution_time=60'; \
        echo 'display_errors=Off'; \
        echo 'log_errors=On'; \
        echo 'error_log=/proc/self/fd/2'; \
        echo 'date.timezone=America/Sao_Paulo'; \
    } > /usr/local/etc/php/conf.d/app.ini

COPY apache-vivencias.conf /etc/apache2/conf-available/vivencias.conf
RUN a2enconf vivencias

COPY app/ /var/www/html/
RUN chown -R www-data:www-data /var/www/html
