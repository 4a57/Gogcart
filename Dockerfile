FROM alpine:3.8 AS roadrunner

ENV RR_VERSION=1.2.6

RUN apk add curl
RUN cd /tmp \
    && curl -OL https://github.com/spiral/roadrunner/releases/download/v${RR_VERSION}/roadrunner-${RR_VERSION}-linux-amd64.tar.gz \
    && tar zxvf roadrunner-${RR_VERSION}-linux-amd64.tar.gz \
    && mv roadrunner-${RR_VERSION}-linux-amd64/rr .

FROM php:7.2 AS symfonyBuild

RUN apt-get update && apt-get install -y \
    curl \
    git \
    zlib1g-dev \
    libicu-dev

RUN docker-php-ext-install intl

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
RUN composer --version

COPY . /application
WORKDIR /application

COPY .env /application/.env

RUN export $(cat .env | grep "^[^#;]") \
    && composer install --prefer-dist --no-progress \
    --no-suggest --quiet --optimize-autoloader --no-interaction \
    && php bin/console cache:clear

FROM php:7.2 AS symfony

RUN apt-get update && apt-get install -y \
    libicu-dev \
    zlib1g-dev

RUN docker-php-ext-install intl \
    && docker-php-ext-install zip

COPY --from=roadrunner /tmp/rr /usr/local/bin/rr
COPY --from=symfonyBuild /application /application
COPY --from=symfonyBuild /usr/local/bin/composer /usr/local/bin/composer

WORKDIR /application

RUN php bin/phpunit --version

COPY .rr.yaml /application/.rr.yaml
COPY .env /application/.env
COPY scripts/entrypoint.sh /application/entrypoint.sh

EXPOSE 8080

ENTRYPOINT ["/application/scripts/entrypoint.sh"]