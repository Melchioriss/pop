FROM jorge07/alpine-php:7.3.5-dev

RUN sh -lc 'xoff'

ARG APP_DIR
ENV PATH="$APP_DIR/bin:$APP_DIR/vendor/bin:${PATH}"

COPY php.ini /etc/php7/conf.d/90-php.ini
COPY php-fpm.conf /etc/php7/php-fpm.conf
