FROM php:8.2-fpm-alpine

ENV NGINX_DOC_ROOT /var/www/app/public

# Install (and cleanup) basic packages
RUN apk update \
    && apk add --no-cache linux-headers libzip libzip-dev $PHPIZE_DEPS autoconf \
    build-base openssl-dev pcre-dev libpq libpq-dev \
    && docker-php-ext-install pdo_pgsql pdo_mysql mysqli sockets \
    && docker-php-ext-install bcmath \
    && docker-php-ext-install pcntl && docker-php-ext-enable pcntl \
    && docker-php-source delete \
    && apk add --no-cache curl supervisor nginx \
    && apk del linux-headers $PHPIZE_DEPS autoconf build-base openssl-dev pcre-dev libpq-dev libzip-dev \
    && rm -rf /var/cache/apk/*

# Create required directories for Nginx and Supervisor
RUN mkdir -p /run/nginx ${NGINX_DOC_ROOT} /var/log/supervisor

# Redirect Nginx logs to stdout/stderr
RUN ln -sf /dev/stdout /var/log/nginx/access.log \
    && ln -sf /dev/stderr /var/log/nginx/error.log

# Use PHP INI production file by default
# You can use a dev or custom version on child images
RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini \
    && echo 'expose_php = off' >> /usr/local/etc/php/conf.d/docker-fpm.ini

# Install Composer to shared location
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/ \
    && ln -s /usr/local/bin/composer.phar /usr/local/bin/composer

# Add unprivileged user
RUN addgroup phpuser -g 1001 \
    && adduser -S -u 1001 -G phpuser -h /home/phpuser -s /bin/bash phpuser \
    && mkdir -p /home/phpuser/.composer/cache /home/phpuser/.ssh \
    && touch /home/phpuser/.ssh/known_hosts \
    && chown -R phpuser:phpuser /home/phpuser

# Copy configuration for Supervisor and Nginx
COPY etc/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY etc/nginx/nginx.conf /etc/nginx/nginx.conf
COPY etc/nginx/server.conf /etc/nginx/conf.d/default.conf

# Copy dummy index PHP file
COPY html/index.php ${NGINX_DOC_ROOT}/index.php

WORKDIR /root

EXPOSE 80 443
ENTRYPOINT ["/usr/bin/supervisord"]
CMD ["-c", "/etc/supervisor/conf.d/supervisord.conf"]
