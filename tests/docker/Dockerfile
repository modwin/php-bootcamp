FROM ghcr.io/mlocati/php-extension-installer:2 AS extension-installer
FROM composer:2 AS composer
FROM php:8.5-cli-bookworm

COPY --from=extension-installer /usr/bin/install-php-extensions /usr/local/bin/
COPY --from=composer /usr/bin/composer /usr/local/bin/composer

RUN apt-get update \
    && apt-get install -y --no-install-recommends git poppler-utils unzip \
    && install-php-extensions curl dom fileinfo gd imap mbstring mysqli pdo_mysql pdo_sqlite simplexml xml zip \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /workspace
