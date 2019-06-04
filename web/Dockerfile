FROM php:7.3.5-fpm-alpine3.9

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

RUN mkdir -p /run/nginx /var/www/html /var/log/supervisor

RUN ln -sf /dev/stdout /var/log/nginx/access.log \
    && ln -sf /dev/stderr /var/log/nginx/error.log

RUN curl -sS https://getcomposer.org/installer | php \
        && mv composer.phar /usr/local/bin/ \
        && ln -s /usr/local/bin/composer.phar /usr/local/bin/composer

COPY etc/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

COPY etc/nginx.conf /etc/nginx/conf.d/default.conf

COPY html/index.html /var/www/html/index.html

COPY html/info.php /var/www/html/info.php

EXPOSE 80 443
ENTRYPOINT ["/usr/bin/supervisord"]
CMD ["-c", "/etc/supervisor/conf.d/supervisord.conf"]
