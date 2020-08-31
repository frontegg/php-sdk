FROM php:7.3-fpm

RUN apt-get update \
  	&& apt-get -y install bash curl \
    && cd  / && rm -fr /src \
    && apt-get -y update -qq \
    && apt-get -y clean > /dev/null \
    && rm -rf \
        /tmp/* \
        /usr/share/doc/* \
        /var/lib/apt/lists/* \
        /var/tmp/* \
        /var/www/*

RUN mkdir /app

WORKDIR /app

## Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

COPY . /app

USER www-data