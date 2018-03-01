FROM php:5.6-apache
MAINTAINER coderfox<coderfox.fu@gmail.com>
COPY . /var/www/html
RUN apt-get update && apt-get install -y --no-install-recommends zlib1g-dev \
 && apt-get clean \
 && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-install zip mysqli
WORKDIR /var/www/html
RUN touch ./setup/install.lock
