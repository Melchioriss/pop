FROM jorge07/alpine-php:7.3-dev

RUN apk add --no-cache --update php7-pdo_sqlite@cast sqlite@cast

RUN sh -lc 'xoff'

ARG APP_DIR
ENV PATH="$APP_DIR/bin:$APP_DIR/vendor/bin:${PATH}"
ENV APP_DIR=$APP_DIR

COPY php.ini /etc/php7/conf.d/90-php.ini
COPY php-fpm.conf /etc/php7/php-fpm.conf

RUN mkdir /etc/supervisor.d
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

COPY crontab /etc/crontabs/root
RUN chown root:root /etc/crontabs/root

CMD ["supervisord", "--nodaemon", "--configuration", "/etc/supervisor/conf.d/supervisord.conf"]
