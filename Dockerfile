FROM php:7.2-apache
RUN a2enmod rewrite
RUN apt update && apt install -y git unzip zip
RUN docker-php-ext-install pdo pdo_mysql 

#PHP Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
COPY . .
RUN composer install