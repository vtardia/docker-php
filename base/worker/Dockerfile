FROM php:7.3.5-cli-alpine3.9

RUN apk update \
    && apk add --no-cache --virtual .mongodb-ext-build-deps autoconf \
    build-base openssl-dev pcre-dev bash \
    && docker-php-ext-install pdo_mysql mysqli sockets \
    && docker-php-ext-install bcmath \
    && docker-php-ext-install pcntl && docker-php-ext-enable pcntl \
    && pecl install mongodb && docker-php-ext-enable mongodb \
    && docker-php-ext-enable pcntl \
    && docker-php-source delete \
    && apk del .mongodb-ext-build-deps autoconf build-base \
    && rm -rf /var/cache/apk/*

RUN curl -sS https://getcomposer.org/installer | php \
        && mv composer.phar /usr/local/bin/ \
        && ln -s /usr/local/bin/composer.phar /usr/local/bin/composer

# Add unprivileged user
RUN addgroup phpuser -g 1001
RUN adduser -S -u 1001 -G phpuser -h /home/phpuser -s /bin/bash phpuser
RUN mkdir -p /home/phpuser/.composer/cache /home/phpuser/.ssh
RUN touch /home/phpuser/.ssh/known_hosts
RUN chown -R phpuser:phpuser /home/phpuser

RUN mkdir -p /usr/local/worker /var/log/worker
RUN chown -R phpuser:phpuser /usr/local/worker /var/log/worker

# Use unprivileged user from now on
USER phpuser
ENV HOME /home/phpuser

# Redirect log file to stdout
RUN ln -sf /dev/stdout /var/log/worker/activity.log

COPY ./bin /usr/local/worker/bin

WORKDIR /usr/local/worker

CMD ["bin/worker"]
