FROM php:7.4-apache

MAINTAINER Erick Sandoval <erick.sr@yahoo.com>

RUN a2enmod rewrite

COPY ./conf/000-default.conf /etc/apache2/sites-available

COPY ./ /var/www/html

RUN chown -R www-data:www-data /var/www

RUN apt-get update

RUN apt-get install -qq git curl nano

RUN apt-get install -qq libzip-dev zip

RUN apt-get clean

RUN docker-php-ext-install pdo_mysql zip pcntl

# Install Composer
RUN curl --silent --show-error https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

#Server name set to localhost
#RUN echo 'ServerName localhost' >> /etc/apache2/apache2.conf

#Installing dependencies
WORKDIR /var/www/html

RUN composer install --prefer-dist --no-progress