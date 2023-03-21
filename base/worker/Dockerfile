FROM php:8.2-cli-alpine

# Install (and cleanup) basic packages
RUN apk update \
    && apk add --no-cache linux-headers libzip-dev $PHPIZE_DEPS autoconf \
    build-base openssl-dev pcre-dev bash \
    && docker-php-ext-install pdo_mysql mysqli sockets \
    && docker-php-ext-install bcmath \
    && docker-php-ext-install pcntl && docker-php-ext-enable pcntl \
    && docker-php-ext-enable pcntl \
    && docker-php-source delete \
    && apk del linux-headers libzip-dev $PHPIZE_DEPS autoconf build-base \
    && rm -rf /var/cache/apk/*

# Use PHP INI production file by default
# You can use a dev or custom version on child images
RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

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

# Use unprivileged user from now on
USER phpuser
ENV HOME /home/phpuser

WORKDIR $HOME

CMD ["php", "-v"]
