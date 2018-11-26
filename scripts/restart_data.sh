#!/bin/sh

php bin/console doctrine:database:drop --force --no-interaction
php bin/console doctrine:database:create --no-interaction
php bin/console doctrine:migration:migrate --no-interaction
php bin/console doctrine:fixtures:load --append