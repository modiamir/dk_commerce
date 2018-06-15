FROM php:7.2-fpm

RUN apt-get update && apt-get install -y \
        zlib1g-dev


RUN pecl install xdebug && \
    docker-php-ext-enable xdebug
COPY ./xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

RUN docker-php-ext-install zip

RUN docker-php-ext-install bcmath

RUN docker-php-ext-install exif

RUN docker-php-ext-install opcache
COPY ./opcache.ini /usr/local/etc/php/conf.d/opcache.ini

RUN apt-get install -y libpng-dev
RUN docker-php-ext-install gd
RUN docker-php-ext-enable gd

RUN docker-php-ext-install pdo_mysql

RUN apt install -y mysql-client

RUN apt-get update && apt-get install -y librabbitmq-dev libssh-dev \
    && docker-php-ext-install opcache bcmath sockets \
    && pecl install amqp \
    && docker-php-ext-enable amqp

RUN apt-get update && apt-get install -y \
    libpq-dev \
    libmemcached-dev \
    curl

RUN curl -L -o /tmp/memcached.tar.gz "https://github.com/php-memcached-dev/php-memcached/archive/php7.tar.gz" \
    && mkdir -p /usr/src/php/ext/memcached \
    && tar -C /usr/src/php/ext/memcached -zxvf /tmp/memcached.tar.gz --strip 1 \
    && docker-php-ext-configure memcached \
    && docker-php-ext-install memcached \
    && rm /tmp/memcached.tar.gz


RUN mkdir -p /usr/src/php/ext/redis \
    && curl -L https://github.com/phpredis/phpredis/archive/3.0.0.tar.gz | tar xvz -C /usr/src/php/ext/redis --strip 1 \
    && echo 'redis' >> /usr/src/php-available-exts \
    && docker-php-ext-install redis

RUN apt-get install -y supervisor

RUN usermod -u 1000 www-data
RUN groupmod -g 1000 www-data
WORKDIR /var/www

COPY supervisord.conf /etc/supervisord.conf

CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisord.conf"]

EXPOSE 9000
