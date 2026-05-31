# syntax=docker/dockerfile:1

FROM dunglas/frankenphp:alpine AS builder

RUN apk add --no-cache \
  git \
  unzip \
  zip \
  libzip-dev \
  libpng-dev \
  libjpeg-turbo-dev \
  freetype-dev \
  icu-dev \
  oniguruma-dev \
  zlib-dev \
  nodejs \
  npm \
  build-base \
  autoconf \
  bash

RUN docker-php-ext-configure gd --with-jpeg=/usr/include --with-freetype=/usr/include \
  && docker-php-ext-install -j$(nproc) pdo_mysql gd intl zip

WORKDIR /app

COPY composer.json composer.lock package.json package-lock.json ./
RUN npm install

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

COPY . .
RUN npm run css:build

FROM dunglas/frankenphp:alpine AS runtime

RUN apk add --no-cache \
  libzip-dev \
  libpng-dev \
  libjpeg-turbo-dev \
  freetype-dev \
  icu-dev \
  oniguruma-dev \
  zlib-dev \
  build-base \
  autoconf \
  && docker-php-ext-configure gd --with-jpeg=/usr/include --with-freetype=/usr/include \
  && docker-php-ext-install -j$(nproc) pdo_mysql gd intl zip \
  && apk del build-base autoconf

WORKDIR /app
COPY --from=builder /app /app
COPY custom-php.ini /usr/local/etc/php/conf.d/custom-php.ini

EXPOSE 80

CMD ["frankenphp", "--host=0.0.0.0", "--port=80", "-t", "public"]
