FROM php:7.3.9-fpm-alpine3.10

ENV NGINX_DOC_ROOT /var/www/app/public

# Install (and cleanup) basic packages
RUN apk update \
    && apk add --no-cache --virtual .mongodb-ext-build-deps autoconf \
    build-base openssl-dev pcre-dev \
    && docker-php-ext-install pdo_mysql mysqli sockets \
    && docker-php-ext-install bcmath \
    && pecl install mongodb && docker-php-ext-enable mongodb \
    && docker-php-source delete \
    && apk add --no-cache curl supervisor nginx \
    && apk del .mongodb-ext-build-deps autoconf build-base \
    && rm -rf /var/cache/apk/*

# Create required directories for Nginx and Supervisor
RUN mkdir -p /run/nginx ${NGINX_DOC_ROOT} /var/log/supervisor

# Redirect Nginx logs to stdout/stderr
RUN ln -sf /dev/stdout /var/log/nginx/access.log \
    && ln -sf /dev/stderr /var/log/nginx/error.log

# Use PHP INI production file by default
# You can use a dev or custom version on child images
RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

# Install Composer to shared location
RUN curl -sS https://getcomposer.org/installer | php \
        && mv composer.phar /usr/local/bin/ \
        && ln -s /usr/local/bin/composer.phar /usr/local/bin/composer

# Add unprivileged user
RUN addgroup phpuser -g 1001
RUN adduser -S -u 1001 -G phpuser -h /home/phpuser -s /bin/bash phpuser
RUN mkdir -p /home/phpuser/.composer/cache /home/phpuser/.ssh
RUN touch /home/phpuser/.ssh/known_hosts
RUN chown -R phpuser:phpuser /home/phpuser

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
