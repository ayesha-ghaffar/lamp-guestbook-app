FROM php:8.1-apache

RUN docker-php-ext-install mysqli

COPY ./app /var/www/html/
EXPOSE 80
