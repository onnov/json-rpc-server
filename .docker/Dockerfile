FROM php:7.4-cli-alpine

RUN apk add --no-cache --update $PHPIZE_DEPS \
    git \
    bash \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    pecl install xdebug-2.9.2 \
	&& docker-php-ext-enable xdebug && \
    apk del $PHPIZE_DEPS && \
    rm -rf /tmp/* /var/tmp/* /usr/share/doc/* /var/cache/apk/* /usr/share/php7
