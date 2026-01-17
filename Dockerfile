FROM php:8.2-apache

RUN docker-php-ext-install mysqli pdo pdo_mysql
RUN pecl install redis mongodb && docker-php-ext-enable redis mongodb

COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
