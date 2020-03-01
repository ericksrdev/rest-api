FROM php:7.4-apache

MAINTAINER Erick Sandoval <erick.sr@yahoo.com>

EXPOSE 9000

RUN a2enmod rewrite

COPY ./conf/000-default.conf /etc/apache2/sites-available

COPY ./ /var/www/html

RUN chown -R www-data:www-data /var/www

RUN apt-get update

RUN apt-get install -qq git curl nano

RUN apt-get install -qq libzip-dev zip

RUN apt-get clean

RUN docker-php-ext-install pdo_mysql zip pcntl

RUN yes | pecl install xdebug \
    && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_enable=1" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_connect_back=1" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_port=9000" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_host=192.168.188.97" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.idekey=PHPStorm" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_handler=dbgp" >> /usr/local/etc/php/conf.d/xdebug.ini

# Install Composer
RUN curl --silent --show-error https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

#Server name set to localhost
#RUN echo 'ServerName localhost' >> /etc/apache2/apache2.conf

#Installing dependencies
WORKDIR /var/www/html

RUN composer install --prefer-dist --no-progress