FROM php:8.1-apache-bullseye
RUN apt-get update && apt-get upgrade -y && rm -rf /var/lib/apt/lists/*
RUN a2enmod rewrite
RUN docker-php-ext-install mysqli pdo pdo_mysql
COPY . /var/www/html/
COPY app/config/apache/000-default.conf /etc/apache2/sites-available/000-default.conf
RUN chown -R www-data:www-data /var/www/html
