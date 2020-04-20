FROM php:7.2-cli-alpine
RUN apk add --no-cache --update $PHPIZE_DEPS \
    git \
    zip \
    bash \
    autoconf \
    nano \
    build-base
RUN apk add --no-cache openssh
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    composer global require hirak/prestissimo && \
    pecl install xdebug-2.7.2 \
	&& docker-php-ext-enable xdebug && \
    apk del $PHPIZE_DEPS && \
    rm -rf /tmp/* /var/tmp/* /usr/share/doc/* /var/cache/apk/* /usr/share/php7
