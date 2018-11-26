FROM alpine:3.8 AS roadrunner

ENV RR_VERSION=1.2.6

RUN apk add curl
RUN cd /tmp \
 && curl -OL https://github.com/spiral/roadrunner/releases/download/v${RR_VERSION}/roadrunner-${RR_VERSION}-linux-amd64.tar.gz \
 && tar zxvf roadrunner-${RR_VERSION}-linux-amd64.tar.gz \
 && mv roadrunner-${RR_VERSION}-linux-amd64/rr .

FROM php:7.2 AS symfonyBuild

RUN apt-get update && apt-get install -y \
        unzip \
        curl \
        gnupg \
        wget \
        git \
        python \
        groff \
        less \
        zlib1g-dev \
        libicu-dev

RUN docker-php-ext-install intl

RUN curl -o /tmp/composer-setup.php https://getcomposer.org/installer \
  && curl -o /tmp/composer-setup.sig https://composer.github.io/installer.sig \
  && php -r "if (hash('SHA384', file_get_contents('/tmp/composer-setup.php')) !== trim(file_get_contents('/tmp/composer-setup.sig'))) { unlink('/tmp/composer-setup.php'); echo 'Invalid installer' . PHP_EOL; exit(1); }" \
  && php /tmp/composer-setup.php

COPY . /application
WORKDIR /application

COPY .env /application/.env

RUN export $(cat .env | grep "^[^#;]") \
    && php /composer.phar install --prefer-dist --no-progress \
    --no-suggest --quiet --optimize-autoloader --no-interaction \
    && php bin/console cache:clear

FROM php:7.2 AS symfony

RUN apt-get update && apt-get install -y libicu-dev
RUN docker-php-ext-install intl

COPY --from=roadrunner /tmp/rr /usr/local/bin/rr

COPY --from=symfonyBuild /application /application
WORKDIR /application

COPY .rr.yaml.dist /application/.rr.yaml
COPY .env /application/.env
COPY scripts/entrypoint.sh /application/entrypoint.sh

EXPOSE 8080

ENTRYPOINT ["/application/scripts/entrypoint.sh"]